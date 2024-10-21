<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
// use Filament\Actions\Action;

use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Milon\Barcode\DNS1D;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation !== 'create') {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));
                                    }),

                                Forms\Components\TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Product::class, 'slug', ignoreRecord: true),

                                Forms\Components\MarkdownEditor::make('description')
                                    ->columnSpan('full'),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Images')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('media')
                                    ->collection('product-images')
                                    ->multiple()
                                    ->maxFiles(5)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif']) 
                                    ->hiddenLabel(),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make('Pricing')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->required(),

                                Forms\Components\TextInput::make('old_price')
                                    ->label('Compare at price')
                                    ->numeric()
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->required(),

                                Forms\Components\TextInput::make('cost')
                                    ->label('Cost per item')
                                    ->helperText('Customers won\'t see this price.')
                                    ->numeric()
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->required(),
                            ])
                            ->columns(2),
                        Forms\Components\Section::make('Inventory')
                            ->schema([
                                Forms\Components\TextInput::make('sku')
                                    ->label('SKU (Stock Keeping Unit)')
                                    ->unique(Product::class, 'sku', ignoreRecord: true)
                                    ->maxLength(255)
                                    ->required(),

                                Forms\Components\TextInput::make('barcode')
                                    ->label('Barcode (ISBN, UPC, GTIN, etc.)')
                                    ->unique(Product::class, 'barcode', ignoreRecord: true)
                                    ->default(fn () => rand(10000000, 99999999))
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),

                                Forms\Components\TextInput::make('qty')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->rules(['integer', 'min:0'])
                                    ->required(),

                                Forms\Components\TextInput::make('security_stock')
                                    ->helperText('The safety stock is the limit stock for your products which alerts you if the product stock will soon be out of stock.')
                                    ->numeric()
                                    ->rules(['integer', 'min:0'])
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Toggle::make('is_visible')
                                    ->label('Visible')
                                    ->helperText('This product will be hidden from all sales channels.')
                                    ->default(true),

                                Forms\Components\DatePicker::make('published_at')
                                    ->label('Availability')
                                    ->default(now())
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('product-image')
                    ->label('Image')
                    ->collection('product-images'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                    

                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visibility')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('barcode')
                    ->label('Barcode')
                    ->formatStateUsing(function ($record) {
                        $barcode = new DNS1D();
                        $productBarcode = $barcode->getBarcodeHTML($record->barcode, 'PHARMA');
                        $productBarcode = $productBarcode . '<div class="w-full text-center"><span>'. $record->barcode ."</span></div>";
                        return $productBarcode;
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('qty')
                    ->label('Quantity')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('security_stock')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publish Date')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('printBarcode')
                ->label('Print Barcode')
                ->action(function ($record) {
                    $barcode = (new DNS1D())->getBarcodePNG($record->barcode, 'C39');
                    
                    return view('filament.resources.products.print-barcode', [
                        'barcode' => $barcode,
                        'product' => $record,
                    ]);
                })
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->modalWidth('md')
                ->modalHeading('Print Barcode')
                ->modalSubheading('Print the barcode for this product.')
                ->form([]) // No form needed
                ->modalContent(fn ($record) => view('filament.resources.products.print-barcode', [
                    'barcode' => (new DNS1D())->getBarcodePNG($record->barcode, 'C39'),
                    'product' => $record,
                ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            // 'view' => Pages\ViewProducts::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}