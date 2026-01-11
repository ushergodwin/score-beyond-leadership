<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-squares-2x2';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'E-Commerce';
    }

    public static function getNavigationSort(): int
    {
        return 2; // Second in E-Commerce
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('product_category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $context, $state, callable $get, callable $set) {
                        // Auto-populate slug if empty or if creating
                        if (($context === 'create' || empty($get('slug'))) && !empty($state)) {
                            $set('slug', Str::slug($state));
                        }
                    }),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('subtitle')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->rows(4),
                Forms\Components\Textarea::make('care_instructions')
                    ->label('Care Instructions')
                    ->rows(3),
                Forms\Components\Textarea::make('materials')
                    ->rows(2),
                Forms\Components\Textarea::make('artisan_story')
                    ->label('Artisan Story')
                    ->rows(3),
                Forms\Components\TextInput::make('base_price')
                    ->numeric()
                    ->required()
                    ->prefix('UGX'),
                Forms\Components\Select::make('base_currency')
                    ->options([
                        'UGX' => 'UGX',
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                    ])
                    ->default('UGX')
                    ->required(),
                Forms\Components\TextInput::make('inventory')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->default('draft')
                    ->required(),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Featured Product'),
                Forms\Components\Toggle::make('is_limited_edition')
                    ->label('Limited Edition')
                    ->live(),
                Forms\Components\TextInput::make('limited_badge_label')
                    ->label('Limited Edition Badge Label')
                    ->maxLength(255)
                    ->visible(fn (callable $get) => $get('is_limited_edition')),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Published At'),
                Forms\Components\FileUpload::make('images')
                    ->label('Product Images')
                    ->multiple()
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->directory('products')
                    ->disk('public')
                    ->visibility('public')
                    ->maxSize(5120) // 5MB
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->helperText('Upload product images. First image will be used as primary. You can reorder images by dragging.')
                    ->reorderable()
                    ->downloadable()
                    ->openable()
                    ->previewable()
                    ->deletable(),
                Forms\Components\Placeholder::make('variants_notice')
                    ->label('')
                    ->content('💡 Tip: After saving this product, you can add variants (e.g., sizes, colors) in the "Variants" tab. Products without variants can still be added to cart by customers.')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('images.url')
                    ->label('Image')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->getStateUsing(function ($record) {
                        $firstImage = $record->images()->orderBy('display_order')->first();
                        return $firstImage?->url ?? null;
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('base_price')
                    ->money(fn ($record) => $record->base_currency ?? 'UGX')
                    ->sortable(),
                Tables\Columns\TextColumn::make('inventory')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record) => $record->inventory <= 5 ? 'danger' : ($record->inventory <= 20 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'archived' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
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
            RelationManagers\VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

