<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\ProductVariant;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Small, Medium, Large, Red, Blue')
                    ->helperText('Variant name (e.g., size, color)'),
                Forms\Components\TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Unique SKU for this variant'),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix(fn (callable $get) => match ($get('currency')) {
                        'USD' => '$',
                        'EUR' => '€',
                        default => 'UGX',
                    })
                    ->helperText('Leave empty to use product base price'),
                Forms\Components\Select::make('currency')
                    ->options([
                        'UGX' => 'UGX (Ugandan Shilling)',
                        'USD' => 'USD (US Dollar)',
                        'EUR' => 'EUR (Euro)',
                    ])
                    ->default('UGX')
                    ->required(),
                Forms\Components\TextInput::make('inventory')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->helperText('Stock quantity for this variant'),
                Forms\Components\KeyValue::make('attributes')
                    ->label('Attributes')
                    ->helperText('Additional variant attributes (e.g., size, color, material)')
                    ->keyLabel('Attribute Name')
                    ->valueLabel('Attribute Value'),
                Forms\Components\Toggle::make('is_default')
                    ->label('Default Variant')
                    ->helperText('This variant will be selected by default'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('sku')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money(fn ($record) => $record->currency ?? 'UGX')
                    ->sortable()
                    ->placeholder('Uses base price'),
                Tables\Columns\TextColumn::make('currency')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'USD' => 'success',
                        'EUR' => 'info',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('inventory')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record) => $record->inventory <= 5 ? 'danger' : ($record->inventory <= 20 ? 'warning' : 'success')),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('is_default', 'desc')
            ->emptyStateHeading('No variants yet')
            ->emptyStateDescription('Add variants to allow customers to choose different options (e.g., sizes, colors). Products without variants can still be added to cart.')
            ->emptyStateIcon('heroicon-o-squares-2x2')
            ->emptyStateActions([
                Actions\CreateAction::make()
                    ->label('Add Variant'),
            ]);
    }
}

