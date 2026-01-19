<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GallerySectionResource\Pages;
use App\Models\GallerySection;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class GallerySectionResource extends Resource
{
    protected static ?string $model = GallerySection::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-photo';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function getNavigationSort(): int
    {
        return 3;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Food Security in Adjumani'),
                Forms\Components\RichEditor::make('description')
                    ->label('Description')
                    ->placeholder('Describe what this project was about...')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'link',
                        'bulletList',
                        'orderedList',
                        'blockquote',
                        'codeBlock',
                        'undo',
                        'redo',
                    ])
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('images')
                    ->label('Gallery Images')
                    ->multiple()
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->directory('gallery')
                    ->disk('public')
                    ->visibility('public')
                    ->maxSize(5120 * 2) // 10MB
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->helperText('Upload gallery images. You can reorder images by dragging.')
                    ->reorderable()
                    ->downloadable()
                    ->openable()
                    ->previewable()
                    ->deletable(),
                Forms\Components\TextInput::make('display_order')
                    ->numeric()
                    ->default(fn() => (\App\Models\GallerySection::max('display_order') ?? -1) + 1)
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
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('images_count')
                    ->label('Images')
                    ->counts('images')
                    ->badge()
                    ->color('success'),
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
            'index' => Pages\ListGallerySections::route('/'),
            'create' => Pages\CreateGallerySection::route('/create'),
            'view' => Pages\ViewGallerySection::route('/{record}'),
            'edit' => Pages\EditGallerySection::route('/{record}/edit'),
        ];
    }
}
