<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomePageFocusAreaResource\Pages;
use App\Models\HomePageFocusArea;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class HomePageFocusAreaResource extends Resource
{
    protected static ?string $model = HomePageFocusArea::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-squares-2x2';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function getNavigationSort(): int
    {
        return 6;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        // Auto-fill image alt text with title if it's empty
                        if (empty($get('image_alt')) && !empty($state)) {
                            $set('image_alt', $state);
                        }
                    }),
                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->maxLength(500),
                Forms\Components\FileUpload::make('image_path')
                    ->label('Image')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->directory('images/home')
                    ->disk('public')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->required()
                    ->previewable()
                    ->downloadable()
                    ->openable()
                    ->deletable()
                    ->helperText('Upload focus area image. Will be stored in storage/images/home'),
                Forms\Components\TextInput::make('image_alt')
                    ->label('Image Alt Text')
                    ->maxLength(255),
                Forms\Components\TextInput::make('display_order')
                    ->numeric()
                    ->default(fn () => (\App\Models\HomePageFocusArea::max('display_order') ?? -1) + 1)
                    ->required()
                    ->helperText('Auto-filled with next available number.'),
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
                Tables\Columns\ImageColumn::make('image_path')
                    ->disk('public')
                    ->size(60),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomePageFocusAreas::route('/'),
            'create' => Pages\CreateHomePageFocusArea::route('/create'),
            'view' => Pages\ViewHomePageFocusArea::route('/{record}'),
            'edit' => Pages\EditHomePageFocusArea::route('/{record}/edit'),
        ];
    }
}

