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
use Filament\Notifications\Notification;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Pesanan';

    protected static ?string $pluralLabel = 'Pesanan';

    protected static ?string $navigationGroup = 'Manajemen Pesanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Header Section - Customer Info
                Forms\Components\Section::make('Informasi Pelanggan')
                    ->description('Pilih jenis pelanggan dan masukkan data')
                    ->icon('heroicon-m-user')
                    ->schema([
                        // Jenis Pelanggan Select
                        Select::make('jenis_pelanggan')
                            ->label('Jenis Pelanggan')
                            ->options([
                                'murid' => '🎓 Murid',
                                'guru' => '👨‍🏫 Guru',
                                'staff' => '👥 Staff',
                            ])
                            ->required()
                            ->prefixIcon('heroicon-m-user-group')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                // Reset semua field yang bergantung pada jenis pelanggan
                                $set('nama_pelanggan', null);
                                $set('saldo_murid', 0);
                                $set('metode_pembayaran', 'tunai');
                                $set('jumlah_bayar', 0);
                                $set('kembalian', 0);
                                $set('kembalian_display', 0);
                            })
                            ->placeholder('Pilih jenis pelanggan...')
                            ->columnSpanFull(),

                        // Conditional Fields berdasarkan jenis pelanggan

                        // MURID SECTION
                        Select::make('nama_pelanggan')
                            ->label('Nama Murid')
                            ->options(function () {
                                return \App\Models\Murid::all()->mapWithKeys(function ($murid) {
                                    return [$murid->name => "{$murid->name} - Kelas {$murid->kelas} (Saldo: Rp " . number_format($murid->saldo, 0, ',', '.') . ")"];
                                });
                            })
                            ->searchable()
                            ->required()
                            ->prefixIcon('heroicon-m-user')
                            ->columnSpanFull()
                            ->live()
                            ->visible(fn (Get $get) => $get('jenis_pelanggan') === 'murid')
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($state) {
                                    $murid = \App\Models\Murid::where('name', $state)->first();
                                    if ($murid) {
                                        $set('saldo_murid', $murid->saldo);
                                    }
                                } else {
                                    $set('saldo_murid', 0);
                                }
                            }),

                        // GURU SECTION
                        Select::make('nama_pelanggan')
                            ->label('Nama Guru')
                            ->options(function () {
                                // Jika ada model Guru, gunakan ini:
                                // return \App\Models\Guru::all()->pluck('name', 'name');

                                // Atau jika menggunakan input manual:
                                return [];
                            })
                            ->searchable()
                            ->required()
                            ->prefixIcon('heroicon-m-user')
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => $get('jenis_pelanggan') === 'guru')
                            ->placeholder('Pilih guru atau ketik nama guru...')
                            ->allowHtml(false)
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Guru')
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data) {
                                return $data['name'];
                            }),

                        // STAFF SECTION
                        Select::make('nama_pelanggan')
                            ->label('Nama Staff')
                            ->options(function () {
                                // Jika ada model Staff, gunakan ini:
                                // return \App\Models\Staff::all()->pluck('name', 'name');

                                // Atau jika menggunakan input manual:
                                return [];
                            })
                            ->searchable()
                            ->required()
                            ->prefixIcon('heroicon-m-user')
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => $get('jenis_pelanggan') === 'staff')
                            ->placeholder('Pilih staff atau ketik nama staff...')
                            ->allowHtml(false)
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Staff')
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data) {
                                return $data['name'];
                            }),

                        // Display current student balance (hanya untuk murid)
                        Forms\Components\Placeholder::make('saldo_display')
                            ->label('Saldo Murid Saat Ini')
                            ->content(function (Get $get): string {
                                $saldo = $get('saldo_murid') ?: 0;
                                return 'Rp ' . number_format($saldo, 0, ',', '.');
                            })
                            ->extraAttributes(['class' => 'text-lg font-semibold text-blue-600'])
                            ->visible(fn (Get $get) => $get('jenis_pelanggan') === 'murid' && !empty($get('nama_pelanggan'))),

                        // Hidden field to store student balance
                        Forms\Components\Hidden::make('saldo_murid'),
                    ])
                    ->collapsed(false)
                    ->collapsible(),

                // Order Items Section - sama seperti sebelumnya
                Forms\Components\Section::make('Daftar Pesanan')
                    ->description('Pilih produk dan atur jumlah pesanan')
                    ->icon('heroicon-m-shopping-cart')
                    ->schema([
                        Repeater::make('orderItems')
                            ->label('')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $items = $get('orderItems') ?: [];
                                $total = 0;

                                foreach ($items as $item) {
                                    $total += $item['subtotal'] ?? 0;
                                }

                                $set('total_harga', $total);

                                // Update kembalian berdasarkan metode pembayaran
                                $metodePembayaran = $get('metode_pembayaran');
                                $jumlahBayar = $get('jumlah_bayar') ?: 0;

                                if ($metodePembayaran === 'saldo' && $get('jenis_pelanggan') === 'murid') {
                                    $saldoMurid = $get('saldo_murid') ?: 0;
                                    $kembalian = $saldoMurid >= $total ? ($saldoMurid - $total) : 0;
                                    $set('jumlah_bayar', $total);
                                } else {
                                    $kembalian = $jumlahBayar >= $total ? ($jumlahBayar - $total) : 0;
                                }

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
                                                        $stokInfo = $product->stok > 0 ?
                                                            " (Stok: {$product->stok})" :
                                                            " (HABIS)";
                                                        return [$product->id => $product->nama_produk . ' - Rp ' . number_format($product->harga, 0, ',', '.') . $stokInfo];
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
                                                        $set('stok_tersedia', $product->stok);
                                                        $jumlah = $get('jumlah') ?: 1;

                                                        if ($product->stok <= 0) {
                                                            $set('jumlah', 0);
                                                            $set('subtotal', 0);
                                                            Notification::make()
                                                                ->title('Stok Habis!')
                                                                ->body("Produk {$product->nama_produk} sedang habis stok.")
                                                                ->danger()
                                                                ->send();
                                                        } elseif ($jumlah > $product->stok) {
                                                            $set('jumlah', $product->stok);
                                                            $set('subtotal', $product->harga * $product->stok);
                                                            Notification::make()
                                                                ->title('Stok Terbatas!')
                                                                ->body("Jumlah pesanan disesuaikan dengan stok tersedia: {$product->stok}")
                                                                ->warning()
                                                                ->send();
                                                        } else {
                                                            $set('subtotal', $product->harga * $jumlah);
                                                        }
                                                    }
                                                } else {
                                                    $set('harga_satuan', 0);
                                                    $set('stok_tersedia', 0);
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
                                                $stokTersedia = $get('stok_tersedia') ?: 0;
                                                $productId = $get('product_id');

                                                if ($productId && $stokTersedia <= 0) {
                                                    $set('jumlah', 0);
                                                    $set('subtotal', 0);
                                                    Notification::make()
                                                        ->title('Stok Habis!')
                                                        ->body('Produk ini sedang habis stok.')
                                                        ->danger()
                                                        ->send();
                                                    return;
                                                }

                                                if ($productId && $jumlah > $stokTersedia) {
                                                    $set('jumlah', $stokTersedia);
                                                    $set('subtotal', $harga * $stokTersedia);
                                                    Notification::make()
                                                        ->title('Stok Terbatas!')
                                                        ->body("Stok tersedia hanya: {$stokTersedia}. Jumlah disesuaikan.")
                                                        ->warning()
                                                        ->send();
                                                } else {
                                                    $set('subtotal', $harga * $jumlah);
                                                }
                                            })
                                            ->hint(function (Get $get): ?string {
                                                $stok = $get('stok_tersedia');
                                                return $stok > 0 ? "Stok tersedia: {$stok}" : null;
                                            })
                                            ->hintColor(function (Get $get): string {
                                                $stok = $get('stok_tersedia') ?: 0;
                                                if ($stok <= 0) return 'danger';
                                                if ($stok <= 5) return 'warning';
                                                return 'success';
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

                                Forms\Components\Hidden::make('harga_satuan'),
                                Forms\Components\Hidden::make('stok_tersedia'),

                                Forms\Components\Placeholder::make('stock_warning')
                                    ->label('')
                                    ->content(function (Get $get): string {
                                        $stok = $get('stok_tersedia') ?: 0;
                                        $productId = $get('product_id');

                                        if (!$productId) return '';

                                        if ($stok <= 0) {
                                            return '🚫 Produk ini habis stok!';
                                        } elseif ($stok <= 5) {
                                            return "⚠️ Stok tinggal {$stok} item!";
                                        }
                                        return '';
                                    })
                                    ->extraAttributes(function (Get $get): array {
                                        $stok = $get('stok_tersedia') ?: 0;
                                        if ($stok <= 0) {
                                            return ['class' => 'text-red-600 font-semibold'];
                                        } elseif ($stok <= 5) {
                                            return ['class' => 'text-orange-600 font-semibold'];
                                        }
                                        return [];
                                    })
                                    ->visible(function (Get $get): bool {
                                        $stok = $get('stok_tersedia') ?: 0;
                                        $productId = $get('product_id');
                                        return $productId && $stok <= 5;
                                    }),

                                Textarea::make('catatan_item')
                                    ->label('Catatan Item')
                                    ->placeholder('Tambahan, kurang pedas, dll...')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $items = $get('orderItems') ?: [];
                            $total = 0;

                            foreach ($items as $item) {
                                $total += $item['subtotal'] ?? 0;
                            }

                            $set('total_harga', $total);

                            $metodePembayaran = $get('metode_pembayaran');
                            $jumlahBayar = $get('jumlah_bayar') ?: 0;

                            if ($metodePembayaran === 'saldo' && $get('jenis_pelanggan') === 'murid') {
                                $saldoMurid = $get('saldo_murid') ?: 0;
                                $kembalian = $saldoMurid >= $total ? ($saldoMurid - $total) : 0;
                                $set('jumlah_bayar', $total);
                            } else {
                                $kembalian = $jumlahBayar >= $total ? ($jumlahBayar - $total) : 0;
                            }

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

                            if ($product) {
                                $stokInfo = $product->stok <= 5 && $product->stok > 0 ? ' ⚠️' : '';
                                $stokInfo .= $product->stok <= 0 ? ' 🚫' : '';
                                return $product->nama_produk . " ({$qty}x) - Rp " . number_format($subtotal, 0, ',', '.') . $stokInfo;
                            }

                            return 'Item Baru';
                        })
                        ->defaultItems(1)
                    ])
                    ->collapsed(false),

                // Summary Section dengan metode pembayaran conditional
                Forms\Components\Section::make('Ringkasan Pesanan')
                    ->description('Total harga dan metode pembayaran')
                    ->icon('heroicon-m-credit-card')
                    ->schema([
                        Forms\Components\Hidden::make('total_harga'),

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
                                    ->options(function (Get $get) {
                                        $options = [
                                            'tunai' => '💵 Tunai',
                                        ];

                                        // Hanya tambahkan opsi saldo jika pelanggan adalah murid
                                        if ($get('jenis_pelanggan') === 'murid') {
                                            $options['saldo'] = '💳 Saldo Murid';
                                        }

                                        return $options;
                                    })
                                    ->default('tunai')
                                    ->required()
                                    ->prefixIcon('heroicon-m-credit-card')
                                    ->columnSpan(1)
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $items = $get('orderItems') ?: [];
                                        $total = 0;

                                        foreach ($items as $item) {
                                            $total += $item['subtotal'] ?? 0;
                                        }

                                        if ($state === 'saldo' && $get('jenis_pelanggan') === 'murid') {
                                            $set('jumlah_bayar', $total);
                                            $saldoMurid = $get('saldo_murid') ?: 0;
                                            $kembalian = $saldoMurid >= $total ? ($saldoMurid - $total) : 0;
                                            $set('kembalian_display', $kembalian);
                                            $set('kembalian', $kembalian);
                                        } else {
                                            $jumlahBayar = $get('jumlah_bayar') ?: 0;
                                            $kembalian = $jumlahBayar >= $total ? ($jumlahBayar - $total) : 0;
                                            $set('kembalian_display', $kembalian);
                                            $set('kembalian', $kembalian);
                                        }

                                        $set('total_harga', $total);
                                    }),

                                TextInput::make('jumlah_bayar')
                                    ->label('Jumlah Bayar')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->live()
                                    ->prefixIcon('heroicon-m-banknotes')
                                    ->columnSpan(1)
                                    ->disabled(fn (Get $get) => $get('metode_pembayaran') === 'saldo' && $get('jenis_pelanggan') === 'murid')
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $items = $get('orderItems') ?: [];
                                        $total = 0;

                                        foreach ($items as $item) {
                                            $total += $item['subtotal'] ?? 0;
                                        }

                                        $metodePembayaran = $get('metode_pembayaran');

                                        if ($metodePembayaran === 'saldo' && $get('jenis_pelanggan') === 'murid') {
                                            $saldoMurid = $get('saldo_murid') ?: 0;
                                            $kembalian = $saldoMurid >= $total ? ($saldoMurid - $total) : 0;
                                        } else {
                                            $jumlahBayar = $state ?: 0;
                                            $kembalian = $jumlahBayar >= $total ? ($jumlahBayar - $total) : 0;
                                        }

                                        $set('kembalian_display', $kembalian);
                                        $set('kembalian', $kembalian);
                                        $set('total_harga', $total);
                                    }),

                                TextInput::make('kembalian_display')
                                    ->label(fn (Get $get) => $get('metode_pembayaran') === 'saldo' && $get('jenis_pelanggan') === 'murid' ? 'SISA SALDO' : 'KEMBALIAN')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->prefixIcon(fn (Get $get) => $get('metode_pembayaran') === 'saldo' && $get('jenis_pelanggan') === 'murid' ? 'heroicon-m-wallet' : 'heroicon-m-gift')
                                    ->extraAttributes(fn (Get $get) => [
                                        'class' => $get('metode_pembayaran') === 'saldo' && $get('jenis_pelanggan') === 'murid' ?
                                            'text-lg font-semibold text-blue-600' :
                                            'text-lg font-semibold text-green-600'
                                    ])
                                    ->columnSpan(1)
                                    ->formatStateUsing(fn ($state) => number_format($state ?: 0, 0, ',', '.')),
                            ]),

                        // Warning if saldo insufficient (hanya untuk murid)
                        Forms\Components\Placeholder::make('saldo_warning')
                            ->label('')
                            ->content('⚠️ Saldo tidak mencukupi untuk pesanan ini!')
                            ->extraAttributes(['class' => 'text-red-600 font-semibold'])
                            ->visible(function (Get $get): bool {
                                if ($get('metode_pembayaran') !== 'saldo' || $get('jenis_pelanggan') !== 'murid') return false;

                                $items = $get('orderItems') ?: [];
                                $total = 0;

                                foreach ($items as $item) {
                                    $total += $item['subtotal'] ?? 0;
                                }

                                $saldoMurid = $get('saldo_murid') ?: 0;
                                return $total > $saldoMurid;
                            }),

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
                        'primary' => 'saldo',
                        'info' => 'transfer',
                        'warning' => 'qris',
                    ]),

                TextColumn::make('tanggal_pesanan')
                    ->label('Tanggal Pesanan')
                    ->dateTime()
                    ->sortable(),

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

                Tables\Filters\SelectFilter::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->options([
                        'tunai' => 'Tunai',
                        'saldo' => 'Saldo Murid',
                        // 'transfer' => 'Transfer',
                        // 'qris' => 'QRIS',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('lihat_items')
                    ->label('Lihat Item')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Item Pesanan')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(function (\App\Models\Order $record) {
                        $items = $record->orderItems()->with('product')->get();

                        return view('filament.modals.lihat-order-items', compact('items'));
                    }),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            // 'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}