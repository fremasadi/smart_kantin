<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Murid;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationLabel = 'Pesanan';
    
    protected static ?string $pluralLabel = 'Pesanan';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            // Header Section - Customer Info
            Forms\Components\Section::make('Informasi Murid')
                ->description('Masukkan data Murid')
                ->icon('heroicon-m-user')
                ->schema([
                    Select::make('nama_pelanggan')
                        ->label('Nama Murid')
                        ->options(
                            \App\Models\Murid::pluck('name', 'name') // key dan value = name
                        )
                        ->searchable()
                        ->required()
                        ->prefixIcon('heroicon-m-user')
                        ->columnSpanFull(),

                ])
                ->collapsed(false)
                ->collapsible(),

            // Order Items Section dengan approach yang berbeda
            Forms\Components\Section::make('Daftar Pesanan')
                ->description('Pilih produk dan atur jumlah pesanan')
                ->icon('heroicon-m-shopping-cart')
                ->schema([
                    Repeater::make('orderItems')
                        ->label('')
                        // ->relationship()
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            // Update total harga saat order items berubah
                            $items = $get('orderItems') ?: [];
                            $total = 0;
                            
                            foreach ($items as $item) {
                                $total += $item['subtotal'] ?? 0;
                            }
                            
                            $set('total_harga', $total);
                            
                            // Update kembalian berdasarkan total yang baru
                            $jumlahBayar = $get('jumlah_bayar') ?: 0;
                            $kembalian = $jumlahBayar >= $total ? ($jumlahBayar - $total) : 0;
                            $set('kembalian_display', $kembalian);
                            $set('kembalian', $kembalian);
                        })
                        ->schema([
                            Forms\Components\Grid::make(4)
                                ->schema([
                                    Select::make('product_id')
                                        ->label('Produk')
                                        ->placeholder('Pilih produk...')
                                        ->options(function () {
                                            return Product::aktif()
                                                ->get()
                                                ->mapWithKeys(function ($product) {
                                                    return [$product->id => $product->nama_produk . ' - Rp ' . number_format($product->harga, 0, ',', '.')];
                                                });
                                        })
                                        ->required()
                                        ->live()
                                        ->searchable()
                                        ->prefixIcon('heroicon-m-cube')
                                        ->columnSpan(2)
                                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                            if ($state) {
                                                $product = Product::find($state);
                                                if ($product) {
                                                    $set('harga_satuan', $product->harga);
                                                    $jumlah = $get('jumlah') ?: 1;
                                                    $set('subtotal', $product->harga * $jumlah);
                                                }
                                            } else {
                                                $set('harga_satuan', 0);
                                                $set('subtotal', 0);
                                            }
                                        }),
                                    
                                    TextInput::make('jumlah')
                                        ->label('Qty')
                                        ->numeric()
                                        ->default(1)
                                        ->required()
                                        ->live()
                                        ->minValue(1)
                                        ->step(1)
                                        ->prefixIcon('heroicon-m-calculator')
                                        ->columnSpan(1)
                                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                            $harga = $get('harga_satuan') ?: 0;
                                            $jumlah = $state ?: 1;
                                            $set('subtotal', $harga * $jumlah);
                                        }),
                                    
                                    TextInput::make('subtotal')
                                        ->label('Subtotal')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->readOnly()
                                        ->columnSpan(1)
                                        ->prefixIcon('heroicon-m-currency-dollar')
                                        ->formatStateUsing(fn ($state) => number_format($state ?: 0, 0, ',', '.')),
                                ]),
                            
                            // Hidden field untuk menyimpan harga satuan
                            Forms\Components\Hidden::make('harga_satuan'),
                            
                            Textarea::make('catatan_item')
                                ->label('Catatan Item')
                                ->placeholder('Tambahan, kurang pedas, dll...')
                                ->rows(2)
                                ->columnSpanFull(),
                        ])
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // Update total harga saat order items berubah
                        $items = $get('orderItems') ?: [];
                        $total = 0;
                        
                        foreach ($items as $item) {
                            $total += $item['subtotal'] ?? 0;
                        }
                        
                        $set('total_harga', $total);
                        
                        // Update kembalian berdasarkan total yang baru
                        $jumlahBayar = $get('jumlah_bayar') ?: 0;
                        $kembalian = $jumlahBayar >= $total ? ($jumlahBayar - $total) : 0;
                        $set('kembalian_display', $kembalian);
                        $set('kembalian', $kembalian);
                    })
                    ->addActionLabel('+ Tambah Item')
                    ->reorderable()
                    ->cloneable()
                    ->collapsible()
                    ->itemLabel(function (array $state): ?string {
                        if (!isset($state['product_id'])) return 'Item Baru';
                        
                        $product = Product::find($state['product_id']);
                        $qty = $state['jumlah'] ?? 1;
                        $subtotal = $state['subtotal'] ?? 0;
                        
                        return $product ? 
                            $product->nama_produk . " ({$qty}x) - Rp " . number_format($subtotal, 0, ',', '.') : 
                            'Item Baru';
                    })
                    ->defaultItems(1)
                ])
                ->collapsed(false),

            // Summary Section dengan computed values
            Forms\Components\Section::make('Ringkasan Pesanan')
                ->description('Total harga dan metode pembayaran')
                ->icon('heroicon-m-credit-card')
                ->schema([
                    // Hidden field untuk total harga
                    Forms\Components\Hidden::make('total_harga'),
                    
                    // Computed total harga
                    Forms\Components\Placeholder::make('computed_total')
                        ->label('TOTAL HARGA')
                        ->content(function (Get $get): string {
                            $items = $get('orderItems') ?: [];
                            $total = 0;
                            
                            foreach ($items as $item) {
                                $total += $item['subtotal'] ?? 0;
                            }
                            
                            return 'Rp ' . number_format($total, 0, ',', '.');
                        })
                        ->extraAttributes(['class' => 'text-2xl font-bold text-primary-600']),
                    
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Select::make('metode_pembayaran')
                                ->label('Metode Pembayaran')
                                ->options([
                                    'tunai' => '💵 Tunai',
                                    // 'transfer' => '🏧 Transfer Bank',
                                    // 'qris' => '📱 QRIS'
                                ])
                                ->default('tunai')
                                ->required()
                                ->prefixIcon('heroicon-m-credit-card')
                                ->columnSpan(1),
                            
                            TextInput::make('jumlah_bayar')
                                ->label('Jumlah Bayar')
                                ->numeric()
                                ->prefix('Rp')
                                ->live()
                                ->prefixIcon('heroicon-m-banknotes')
                                ->columnSpan(1)
                                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                    // Hitung total dari orderItems
                                    $items = $get('orderItems') ?: [];
                                    $total = 0;
                                    
                                    foreach ($items as $item) {
                                        $total += $item['subtotal'] ?? 0;
                                    }
                                    
                                    // Hitung kembalian
                                    $jumlahBayar = $state ?: 0;
                                    $kembalian = $jumlahBayar >= $total ? ($jumlahBayar - $total) : 0;
                                    
                                    // Set kembalian ke field yang visible
                                    $set('kembalian_display', $kembalian);
                                    // Set juga ke hidden field untuk save
                                    $set('kembalian', $kembalian);
                                    // Update total_harga hidden field
                                    $set('total_harga', $total);
                                }),
                            
                            // Gunakan TextInput readonly untuk kembalian agar bisa update real-time
                            TextInput::make('kembalian_display')
                                ->label('KEMBALIAN')
                                ->numeric()
                                ->prefix('Rp')
                                ->readOnly()
                                ->prefixIcon('heroicon-m-gift')
                                ->extraAttributes(['class' => 'text-lg font-semibold text-green-600'])
                                ->columnSpan(1)
                                ->formatStateUsing(fn ($state) => number_format($state ?: 0, 0, ',', '.')),
                        ]),
                    
                    // Hidden field untuk kembalian (akan diisi saat save)
                    Forms\Components\Hidden::make('kembalian'),
                ])
                ->collapsed(false),
        ])
        ->columns(1);
}
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_pesanan')
                    ->label('Nomor Pesanan')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->money('IDR')
                    ->sortable(),
                
                BadgeColumn::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->colors([
                        'success' => 'tunai',
                        'info' => 'transfer',
                        'warning' => 'qris',
                    ]),
                
                TextColumn::make('tanggal_pesanan')
                    ->label('Tanggal Pesanan')
                    ->dateTime()
                    ->sortable(),
                
                // TextColumn::make('orderItems_count')
                //     ->label('Jumlah Item')
                //     ->counts('orderItems')
                //     ->badge(),
                
                // TextColumn::make('created_at')
                //     ->label('Dibuat')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            
                Tables\Filters\Filter::make('tanggal_pesanan')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari_tanggal'], fn ($query, $date) => $query->whereDate('tanggal_pesanan', '>=', $date))
                            ->when($data['sampai_tanggal'], fn ($query, $date) => $query->whereDate('tanggal_pesanan', '<=', $date));
                    }),
            ])
            ->actions([
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}