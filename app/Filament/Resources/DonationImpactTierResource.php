<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationImpactTierResource\Pages;
use App\Models\DonationImpactTier;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DonationImpactTierResource extends Resource
{
    protected static ?string $model = DonationImpactTier::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-currency-dollar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Donations';
    }

    public static function getNavigationSort(): int
    {
        return 2; // Second in Donations
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Supporter, Champion, Leader, Hero'),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->prefix(fn (callable $get) => match ($get('currency')) {
                        'USD' => '$',
                        'EUR' => '€',
                        default => 'UGX',
                    }),
                Forms\Components\Select::make('currency')
                    ->options([
                        'UGX' => 'UGX (Ugandan Shilling)',
                        'USD' => 'USD (US Dollar)',
                        'EUR' => 'EUR (Euro)',
                    ])
                    ->default('UGX')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->rows(3)
                    ->maxLength(500)
                    ->placeholder('Describe what this donation tier supports'),
                Forms\Components\TextInput::make('display_order')
                    ->numeric()
                    ->default(fn () => (\App\Models\DonationImpactTier::max('display_order') ?? -1) + 1)
                    ->required()
                    ->helperText('Lower numbers appear first. Auto-filled with next available number.'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('amount')
                    ->money(fn ($record) => $record->currency ?? 'UGX')
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'USD' => 'success',
                        'EUR' => 'info',
                        default => 'primary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('display_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('currency')
                    ->options([
                        'UGX' => 'UGX',
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('display_order');
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
            'index' => Pages\ListDonationImpactTiers::route('/'),
            'create' => Pages\CreateDonationImpactTier::route('/create'),
            'view' => Pages\ViewDonationImpactTier::route('/{record}'),
            'edit' => Pages\EditDonationImpactTier::route('/{record}/edit'),
        ];
    }
}

