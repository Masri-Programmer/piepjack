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

    protected static ?string $modelLabel = 'Bewertung';

    protected static ?string $pluralModelLabel = 'Bewertungen';

    // 2. Name it in German to match the rest of your menu
    protected static ?string $navigationLabel = 'Bewertungen';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Bewertungsdetails')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('Benutzer')
                                    ->relationship('user', 'email')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\Select::make('product_id')
                                    ->label('Produkt-ID')
                                    ->relationship('product', 'id')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\Select::make('rating')
                                    ->label('Bewertung')
                                    ->options([
                                        1 => '1 - Schlecht',
                                        2 => '2 - Ausreichend',
                                        3 => '3 - Gut',
                                        4 => '4 - Sehr Gut',
                                        5 => '5 - Hervorragend',
                                    ])
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('title')
                                    ->label('Titel')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('comment')
                                    ->label('Kommentar')
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
                                    ->label('Freigegeben')
                                    ->default(false)
                                    ->helperText('Aktivieren, um die Bewertung im Shop anzuzeigen.'),
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
                    ->label('Benutzer-Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product_id')
                    ->label('Produkt-ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Bewertung')
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        1, 2 => 'danger',
                        3 => 'warning',
                        4, 5 => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\ToggleColumn::make('is_approved')
                    ->label('Freigegeben')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Erstellt am')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Freigabestatus'),

                Tables\Filters\SelectFilter::make('rating')
                    ->label('Bewertung')
                    ->options([
                        1 => '1 Stern',
                        2 => '2 Sterne',
                        3 => '3 Sterne',
                        4 => '4 Sterne',
                        5 => '5 Sterne',
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
