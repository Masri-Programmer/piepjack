<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReturningResource\Pages;
use App\Models\Returning;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Facades\Payments;
use Lunar\Models\Transaction;

class ReturningResource extends Resource
{
    protected static ?string $model = Returning::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Order Management';

    protected static ?string $modelLabel = 'Return Request';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_id')->disabled(),
                Forms\Components\TextInput::make('status'),
                Forms\Components\Textarea::make('reason')->disabled(),
                Forms\Components\TextInput::make('return_fee')
                    ->numeric()
                    ->prefix('€'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('order.reference')->label('Order Ref'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'requested' => 'warning',
                        'refunded' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('return_fee')->money('eur'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Here is the custom action that connects to Lunar Payments
                Tables\Actions\Action::make('processRefund')
                    ->label('Process & Refund')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Process Return & Issue Refund')
                    ->modalDescription('This will deduct the return fee and refund the remainder to the original payment method.')
                    ->visible(fn (Returning $record) => $record->status === 'requested')
                    ->action(function (Returning $record) {

                        // 1. Find the Lunar Capture Transaction
                        $capture = Transaction::where('order_id', $record->order_id)
                            ->where('type', 'capture')
                            ->where('success', true)
                            ->first();

                        if (! $capture) {
                            Notification::make()->title('No capture transaction found')->danger()->send();

                            return;
                        }

                        // 2. Calculate the refund amount in cents
                        // Assuming return_fee is stored in euros (e.g., 4.90)
                        $refundAmountCents = $capture->amount->value - ($record->return_fee * 100);

                        // 3. Process with Lunar
                        $response = Payments::driver('stripe')->refund(
                            $capture,
                            $refundAmountCents,
                            "RMA #{$record->id} processed"
                        );

                        // 4. Handle Response
                        if ($response->success) {
                            $record->update(['status' => 'refunded']);
                            Notification::make()->title('Refund successful')->success()->send();
                        } else {
                            Notification::make()->title('Refund failed')->danger()->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageReturnings::route('/'),
        ];
    }
}
