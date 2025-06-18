<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

// class LowStockProducts extends BaseWidget
// {
//     protected static ?string $heading = 'Produk Stok Rendah';
//     protected static ?int $sort = 5;
//     protected int | string | array $columnSpan = 'full';

//     public function table(Table $table): Table
//     {
//         return $table
//             ->query(
//                 Product::query()
//                     ->where('stok', '<=', 10)
//                     ->where('status', 'aktif')
//                     ->orderBy('stok', 'asc')
//             )
//             ->columns([
//                 Tables\Columns\ImageColumn::make('gambar')
//                     ->label('Gambar')
//                     ->square()
//                     ->size(50),
                
//                 Tables\Columns\TextColumn::make('nama_produk')
//                     ->label('Nama Produk')
//                     ->searchable()
//                     ->weight('bold'),
                
//                 Tables\Columns\TextColumn::make('kategori')
//                     ->label('Kategori')
//                     ->badge(),
                
//                 Tables\Columns\TextColumn::make('stok')
//                     ->label('Stok')
//                     ->badge()
//                     ->color(fn (int $state): string => match (true) {
//                         $state <= 5 => 'danger',
//                         $state <= 10 => 'warning',
//                         default => 'success',
//                     }),
                
//                 Tables\Columns\TextColumn::make('harga')
//                     ->label('Harga')
//                     ->money('IDR'),
                
//                 Tables\Columns\TextColumn::make('supplier.name')
//                     ->label('Supplier')
//                     ->default('Tidak Ada'),
//             ])
//             ->actions([
//                 Tables\Actions\Action::make('edit')
//                     ->label('Edit')
//                     ->icon('heroicon-o-pencil')
//                     ->url(fn (Product $record): string => route('filament.admin.resources.products.edit', $record))
//                     ->openUrlInNewTab(),
//             ]);
//     }
// }