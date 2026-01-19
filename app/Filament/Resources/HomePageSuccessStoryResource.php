<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomePageSuccessStoryResource\Pages;
use App\Models\HomePageSuccessStory;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class HomePageSuccessStoryResource extends Resource
{
    protected static ?string $model = HomePageSuccessStory::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-star';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function getNavigationSort(): int
    {
        return 7;
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
                    ->required()
                    ->rows(8)
                    ->maxLength(2000)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('quote')
                    ->label('Quote/Tagline')
                    ->rows(2)
                    ->maxLength(500)
                    ->helperText('Optional quote or tagline (e.g., "From the Classroom to the Field")'),
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
                    ->maxSize(5120 * 2) // 10 MB
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->required()
                    ->previewable()
                    ->downloadable()
                    ->openable()
                    ->deletable()
                    ->helperText('Upload success story image. Will be stored in storage/images/home'),
                Forms\Components\TextInput::make('image_alt')
                    ->label('Image Alt Text')
                    ->maxLength(255),
                Forms\Components\TextInput::make('display_order')
                    ->numeric()
                    ->default(fn() => (\App\Models\HomePageSuccessStory::max('display_order') ?? -1) + 1)
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
                Tables\Columns\TextColumn::make('quote')
                    ->limit(50)
                    ->toggleable(),
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
            'index' => Pages\ListHomePageSuccessStories::route('/'),
            'create' => Pages\CreateHomePageSuccessStory::route('/create'),
            'view' => Pages\ViewHomePageSuccessStory::route('/{record}'),
            'edit' => Pages\EditHomePageSuccessStory::route('/{record}/edit'),
        ];
    }
}
