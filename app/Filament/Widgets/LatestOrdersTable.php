<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

// class LatestOrdersTable extends BaseWidget
// {
//     protected static ?string $heading = 'Pesanan Terbaru';
//     protected static ?int $sort = 4;
//     protected int | string | array $columnSpan = 'full';

//     public function table(Table $table): Table
//     {
//         return $table
//             ->query(
//                 Order::query()
//                     ->with('orderItems.product')
//                     ->latest('tanggal_pesanan')
//                     ->limit(10)
//             )
//             ->columns([
//                 Tables\Columns\TextColumn::make('nomor_pesanan')
//                     ->label('No. Pesanan')
//                     ->searchable()
//                     ->copyable(),
                
//                 Tables\Columns\TextColumn::make('nama_pelanggan')
//                     ->label('Pelanggan')
//                     ->searchable(),
                
//                 Tables\Columns\TextColumn::make('total_harga')
//                     ->label('Total')
//                     ->money('IDR')
//                     ->sortable(),
                
//                 Tables\Columns\TextColumn::make('metode_pembayaran')
//                     ->label('Pembayaran')
//                     ->badge()
//                     ->color(fn (string $state): string => match ($state) {
//                         'tunai' => 'success',
//                         'kartu' => 'info',
//                         'transfer' => 'warning',
//                         default => 'gray',
//                     }),
                
//                 Tables\Columns\TextColumn::make('tanggal_pesanan')
//                     ->label('Tanggal')
//                     ->dateTime('d/m/Y H:i')
//                     ->sortable(),
                
//                 Tables\Columns\TextColumn::make('orderItems')
//                     ->label('Jumlah Item')
//                     ->getStateUsing(fn (Order $record): int => $record->orderItems->sum('jumlah'))
//                     ->badge()
//                     ->color('primary'),
//             ])
//             ->actions([
//                 // Tables\Actions\Action::make('view')
//                 //     ->label('Detail')
//                 //     ->icon('heroicon-o-eye')
//                 //     ->url(fn (Order $record): string => route('filament.admin.resources.orders.view', $record))
//                 //     ->openUrlInNewTab(),
//             ]);
//     }
// }
