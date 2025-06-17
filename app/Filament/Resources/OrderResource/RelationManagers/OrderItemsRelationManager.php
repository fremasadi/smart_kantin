<?php

// ==============================================
// RELATION MANAGER
// ==============================================

// File: app/Filament/Resources/OrderResource/RelationManagers/OrderItemsRelationManager.php
namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Product;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderItems';

    protected static ?string $title = 'Item Pesanan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Select::make('product_id')
                            ->label('Produk')
                            ->relationship('product', 'nama_produk')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $product = Product::find($state);
                                    $set('harga_satuan', $product?->harga ?? 0);
                                }
                            }),
                            
                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(function ($state, $get, callable $set) {
                                $jumlah = (int) $state;
                                $harga = (float) $get('harga_satuan');
                                $set('subtotal', $jumlah * $harga);
                            }),
                    ]),
                    
                Grid::make(2)
                    ->schema([
                        TextInput::make('harga_satuan')
                            ->label('Harga Satuan')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $get, callable $set) {
                                $jumlah = (int) $get('jumlah');
                                $harga = (float) $state;
                                $set('subtotal', $jumlah * $harga);
                            }),
                            
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(),
                    ]),
                    
                Textarea::make('catatan_item')
                    ->label('Catatan Item')
                    ->placeholder('Catatan khusus untuk item ini...')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.nama_produk')
            ->columns([
                TextColumn::make('product.nama_produk')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->suffix(' pcs')
                    ->alignCenter(),
                    
                TextColumn::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->money('IDR')
                    ->alignRight(),
                    
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('IDR')
                    ->alignRight()
                    ->weight('bold'),
                    
                TextColumn::make('catatan_item')
                    ->label('Catatan')
                    ->limit(30)
                    ->placeholder('Tidak ada catatan'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Item'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}