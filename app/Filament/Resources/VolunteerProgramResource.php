<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VolunteerProgramResource\Pages;
use App\Models\VolunteerProgram;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class VolunteerProgramResource extends Resource
{
    protected static ?string $model = VolunteerProgram::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-briefcase';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Programs';
    }

    public static function getNavigationSort(): int
    {
        return 0; // Before Volunteer Applications
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('slug')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                    ])
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Unique identifier for the program type'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Paid Volunteers'),
                Forms\Components\TextInput::make('badge')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Paid'),
                Forms\Components\Textarea::make('summary')
                    ->required()
                    ->rows(3)
                    ->maxLength(500)
                    ->helperText('Brief summary displayed on the volunteer page'),
                Forms\Components\Textarea::make('description')
                    ->rows(4)
                    ->maxLength(1000)
                    ->helperText('Detailed description of the program'),
                Forms\Components\Repeater::make('benefits')
                    ->schema([
                        Forms\Components\TextInput::make('benefit')
                            ->label('Benefit')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->defaultItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['benefit'] ?? null)
                    ->collapsible()
                    ->helperText('List of benefits for this program'),
                Forms\Components\Repeater::make('logistics')
                    ->schema([
                        Forms\Components\TextInput::make('logistic')
                            ->label('Logistic')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->defaultItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['logistic'] ?? null)
                    ->collapsible()
                    ->helperText('List of logistics information'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->required(),
                Forms\Components\TextInput::make('display_order')
                    ->numeric()
                    ->default(fn () => (\App\Models\VolunteerProgram::max('display_order') ?? -1) + 1)
                    ->required()
                    ->helperText('Lower numbers appear first. Auto-filled with next available number.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('badge')
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('slug')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
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
            'index' => Pages\ListVolunteerPrograms::route('/'),
            'create' => Pages\CreateVolunteerProgram::route('/create'),
            'view' => Pages\ViewVolunteerProgram::route('/{record}'),
            'edit' => Pages\EditVolunteerProgram::route('/{record}/edit'),
        ];
    }
}

