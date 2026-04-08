<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductReviewResource\Pages;
use App\Models\ProductReview;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductReviewResource extends Resource
{
    protected static ?string $model = ProductReview::class;

    // 1. A star icon makes sense for reviews
    protected static ?string $navigationIcon = 'heroicon-o-star';

    // 2. Name it in German to match the rest of your menu
    protected static ?string $navigationLabel = 'Bewertungen';

    // 3. MATCH THIS EXACTLY to the heading in your screenshot
    protected static ?string $navigationGroup = 'Katalog';

    // 4. Sets the order (higher number = lower in the list)
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Review Details')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'email')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'id')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\Select::make('rating')
                                    ->options([
                                        1 => '1 - Poor',
                                        2 => '2 - Fair',
                                        3 => '3 - Good',
                                        4 => '4 - Very Good',
                                        5 => '5 - Excellent',
                                    ])
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('title')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('comment')
                                    ->required()
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])->columnSpan(['lg' => fn (?ProductReview $record) => $record === null ? 3 : 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Moderation')
                            ->schema([
                                Forms\Components\Toggle::make('is_approved')
                                    ->label('Approved for display')
                                    ->default(false)
                                    ->helperText('Turn this on to show the review on the storefront.'),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.email')
                    ->label('User Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product_id') // Adjust to product.name if you set up a custom accessor in Lunar
                    ->label('Product ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rating')
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        1, 2 => 'danger',
                        3 => 'warning',
                        4, 5 => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\ToggleColumn::make('is_approved')
                    ->label('Approved')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Approval Status'),

                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        1 => '1 Star',
                        2 => '2 Stars',
                        3 => '3 Stars',
                        4 => '4 Stars',
                        5 => '5 Stars',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductReviews::route('/'),
            'create' => Pages\CreateProductReview::route('/create'),
            'edit' => Pages\EditProductReview::route('/{record}/edit'),
        ];
    }
}
