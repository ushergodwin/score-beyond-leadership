<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingMethodResource\Pages;
use App\Models\ShippingMethod;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ShippingMethodResource extends Resource
{
    protected static ?string $model = ShippingMethod::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-truck';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'E-Commerce';
    }

    public static function getNavigationSort(): int
    {
        return 6; // Sixth in E-Commerce
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('type')
                    ->options([
                        'standard' => 'Standard',
                        'express' => 'Express',
                        'overnight' => 'Overnight',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('region')
                    ->maxLength(255),
                Forms\Components\TextInput::make('carrier')
                    ->maxLength(255),
                Forms\Components\TextInput::make('base_rate')
                    ->numeric()
                    ->required()
                    ->prefix('UGX'),
                Forms\Components\Select::make('currency')
                    ->options([
                        'UGX' => 'UGX',
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                    ])
                    ->default('UGX')
                    ->required(),
                Forms\Components\TextInput::make('estimated_min_days')
                    ->label('Min Days')
                    ->numeric(),
                Forms\Components\TextInput::make('estimated_max_days')
                    ->label('Max Days')
                    ->numeric(),
                Forms\Components\Toggle::make('is_pickup')
                    ->label('Is Pickup'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('region')
                    ->searchable(),
                Tables\Columns\TextColumn::make('base_rate')
                    ->money(fn ($record) => $record->currency ?? 'UGX')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_min_days')
                    ->label('Min Days'),
                Tables\Columns\TextColumn::make('estimated_max_days')
                    ->label('Max Days'),
                Tables\Columns\IconColumn::make('is_pickup')
                    ->boolean()
                    ->label('Pickup'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'standard' => 'Standard',
                        'express' => 'Express',
                        'overnight' => 'Overnight',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\TernaryFilter::make('is_pickup')
                    ->label('Pickup'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListShippingMethods::route('/'),
            'create' => Pages\CreateShippingMethod::route('/create'),
            'edit' => Pages\EditShippingMethod::route('/{record}/edit'),
        ];
    }
}

