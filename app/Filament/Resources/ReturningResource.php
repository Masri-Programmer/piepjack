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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Lunar\Facades\Payments;
use Lunar\Models\Transaction;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class ReturningResource extends Resource
{
    protected static ?string $model = Returning::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Verkauf';

    protected static ?string $modelLabel = 'Rückgabe';

    protected static ?string $pluralModelLabel = 'Rückgaben';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_id')
                    ->label('Bestell-ID')
                    ->disabled(),
                Forms\Components\TextInput::make('status')
                    ->label('Status'),
                Forms\Components\Textarea::make('reason')
                    ->label('Grund')
                    ->disabled(),
                Forms\Components\TextInput::make('return_fee')
                    ->label('Rückgebühr')
                    ->numeric()
                    ->prefix('€'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order.reference')
                    ->label('Bestell-Ref')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'requested' => 'warning',
                        'refunded' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'requested' => 'Angefragt',
                        'refunded' => 'Erstattet',
                        'rejected' => 'Abgelehnt',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('return_fee')
                    ->label('Rückgebühr')
                    ->money('eur'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Here is the custom action that connects to Lunar Payments
                Tables\Actions\Action::make('processRefund')
                    ->label('Erstatten')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Rückgabe bearbeiten & Rückerstattung veranlassen')
                    ->modalDescription('Dadurch wird die Rückgebühr abgezogen und der Restbetrag auf die ursprüngliche Zahlungsmethode zurückerstattet.')
                    ->visible(fn (Returning $record) => $record->status === 'requested')
                    ->action(function (Returning $record) {
                        // 1. Find the Lunar Capture Transaction
                        $capture = Transaction::where('order_id', $record->order_id)
                            ->where('type', 'capture')
                            ->where('success', true)
                            ->first();

                        if (! $capture || ! Str::startsWith($capture->reference, ['pi_', 'ch_', 'py_'])) {
                            Notification::make()
                                ->title('Keine gültige Stripe-Zahlung gefunden')
                                ->body('Die Transaktionsreferenz fehlt oder ist ungültig.')
                                ->danger()
                                ->send();

                            return;
                        }

                        Stripe::setApiKey(config('services.stripe.secret'));

                        // CRITICAL FIX: If we have a PaymentIntent ID (pi_...), we MUST resolve the Charge ID (ch_...)
                        // because Lunar's Stripe driver passes the reference to Stripe's Charge Refund API.
                        if (Str::startsWith($capture->reference, 'pi_')) {
                            try {
                                $pi = PaymentIntent::retrieve($capture->reference);
                                if ($pi->latest_charge) {
                                    $capture->update(['reference' => $pi->latest_charge]);
                                    $capture = $capture->fresh();
                                }
                            } catch (\Exception $e) {
                                Log::error("Failed to resolve Charge ID for PI {$capture->reference}: ".$e->getMessage());
                            }
                        }

                        // 2. Calculate the refund amount in cents
                        $refundAmountCents = (int) ($capture->amount->value - ($record->return_fee * 100));

                        if ($refundAmountCents <= 0) {
                            Notification::make()
                                ->title('Ungültiger Rückerstattungsbetrag')
                                ->body('Die Rückgebühr übersteigt den Gesamtbestellwert.')
                                ->danger()
                                ->send();

                            return;
                        }

                        try {
                            // 3. Process with Lunar
                            $response = Payments::driver('stripe')->refund(
                                $capture,
                                $refundAmountCents,
                                "RMA #{$record->id} bearbeitet"
                            );

                            // 4. Handle Response
                            if ($response->success) {
                                $record->update(['status' => 'refunded']);
                                Notification::make()->title('Rückerstattung erfolgreich')->success()->send();
                            } else {
                                Notification::make()->title('Rückerstattung fehlgeschlagen')->body($response->message ?? 'Unbekannter Fehler')->danger()->send();
                            }
                        } catch (\Exception $e) {
                            Log::error("Refund error for Returning ID {$record->id}: ".$e->getMessage());
                            Notification::make()
                                ->title('Stripe Rückerstattungsfehler')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
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
