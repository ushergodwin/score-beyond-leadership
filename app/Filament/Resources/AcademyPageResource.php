<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademyPageResource\Pages;
use App\Models\AcademyPage;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AcademyPageResource extends Resource
{
    protected static ?string $model = AcademyPage::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-academic-cap';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function getNavigationSort(): int
    {
        return 4; // After Gallery Sections
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('slug')
                    ->default('academy')
                    ->required()
                    ->maxLength(255)
                    ->disabled()
                    ->helperText('This is the unique identifier for the Academy page'),
                
                // Hero Section
                Forms\Components\TextInput::make('hero_title')
                    ->label('Hero Title')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Score Beyond Academy'),
                Forms\Components\Textarea::make('hero_subtitle')
                    ->label('Hero Subtitle')
                    ->required()
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('e.g., We don\'t just build athletes we build future leaders.'),
                
                // What the Academy Offers
                Forms\Components\TextInput::make('offers_heading')
                    ->label('Offers Section Heading')
                    ->maxLength(255)
                    ->placeholder('e.g., What the Academy Offers'),
                Forms\Components\Textarea::make('offers_description')
                    ->label('Offers Description')
                    ->required()
                    ->rows(4)
                    ->maxLength(1000),
                Forms\Components\Repeater::make('offerings')
                    ->schema([
                        Forms\Components\TextInput::make('icon')
                            ->label('Icon Class')
                            ->placeholder('e.g., bi-trophy-fill')
                            ->helperText('Bootstrap icon class name')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('label')
                            ->label('Label')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->defaultItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                    ->collapsible()
                    ->helperText('List of offerings (e.g., Sports camps, Personal Growth)'),
                Forms\Components\TextInput::make('location')
                    ->maxLength(255)
                    ->placeholder('e.g., Hill Preparatory School Naguru'),
                
                // Why It Matters
                Forms\Components\TextInput::make('why_matters_heading')
                    ->label('Why It Matters Heading')
                    ->maxLength(255)
                    ->placeholder('e.g., Why It Matters'),
                Forms\Components\Textarea::make('why_matters_description')
                    ->label('Why It Matters Description')
                    ->required()
                    ->rows(4)
                    ->maxLength(1000),
                
                // Join the Academy
                Forms\Components\TextInput::make('join_heading')
                    ->label('Join Section Heading')
                    ->maxLength(255)
                    ->placeholder('e.g., Join the Academy'),
                Forms\Components\Textarea::make('join_description')
                    ->label('Join Description')
                    ->required()
                    ->rows(3)
                    ->maxLength(500),
                Forms\Components\TextInput::make('join_cta_text')
                    ->label('Join CTA Button Text')
                    ->default('Apply Now')
                    ->maxLength(255),
                
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hero_title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Last Updated'),
            ])
            ->filters([
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
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademyPages::route('/'),
            'view' => Pages\ViewAcademyPage::route('/{record}'),
            'edit' => Pages\EditAcademyPage::route('/{record}/edit'),
        ];
    }
}

