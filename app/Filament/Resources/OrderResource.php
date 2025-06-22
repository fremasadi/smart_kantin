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
                                \App\Models\Murid::all()->mapWithKeys(function ($murid) {
                                    return [$murid->name => "{$murid->name} - Kelas {$murid->kelas} (Saldo: Rp " . number_format($murid->saldo, 0, ',', '.') . ")"];
                                })
                            )
                            ->searchable()
                            ->required()
                            ->prefixIcon('heroicon-m-user')
                            ->columnSpanFull()
                            ->live()
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

                        // Display current student balance
                        Forms\Components\Placeholder::make('saldo_display')
                            ->label('Saldo Murid Saat Ini')
                            ->content(function (Get $get): string {
                                $saldo = $get('saldo_murid') ?: 0;
                                return 'Rp ' . number_format($saldo, 0, ',', '.');
                            })
                            ->extraAttributes(['class' => 'text-lg font-semibold text-blue-600'])
                            ->visible(fn (Get $get) => !empty($get('nama_pelanggan'))),

                        // Hidden field to store student balance
                        Forms\Components\Hidden::make('saldo_murid'),
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
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                // Update total harga saat order items berubah
                                $items = $get('orderItems') ?: [];
                                $total = 0;
                                
                                foreach ($items as $item) {
                                    $total += $item['subtotal'] ?? 0;
                                }
                                
                                $set('total_harga', $total);
                                
                                // Update kembalian berdasarkan metode pembayaran
                                $metodePembayaran = $get('metode_pembayaran');
                                $jumlahBayar = $get('jumlah_bayar') ?: 0;
                                
                                if ($metodePembayaran === 'saldo') {
                                    $saldoMurid = $get('saldo_murid') ?: 0;
                                    $kembalian = $saldoMurid >= $total ? ($saldoMurid - $total) : 0;
                                    $set('jumlah_bayar', $total); // Set jumlah bayar sama dengan total untuk saldo
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
                                                        $stockStatus = $product->stok <= 0 ? ' - ❌ HABIS' : 
                                                                      ($product->stok <= 5 ? ' - ⚠️ STOK RENDAH' : '');
                                                        return [$product->id => $product->nama_produk . ' - Rp ' . number_format($product->harga, 0, ',', '.') . ' (Stok: ' . $product->stok . ')' . $stockStatus];
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
                                                        
                                                        // Validasi jika jumlah melebihi stok
                                                        if ($jumlah > $product->stok) {
                                                            $set('jumlah', $product->stok);
                                                            $jumlah = $product->stok;
                                                            
                                                            if ($product->stok == 0) {
                                                                Notification::make()
                                                                    ->title('Produk Habis!')
                                                                    ->body("Produk {$product->nama_produk} sedang habis stok")
                                                                    ->warning()
                                                                    ->duration(4000)
                                                                    ->send();
                                                            } else {
                                                                Notification::make()
                                                                    ->title('Stok Terbatas!')
                                                                    ->body("Stok {$product->nama_produk} hanya tersedia {$product->stok} unit")
                                                                    ->warning()
                                                                    ->duration(4000)
                                                                    ->send();
                                                            }
                                                        }
                                                        
                                                        $set('subtotal', $product->harga * $jumlah);
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
                                            ->rule(function (Get $get) {
                                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                    $productId = $get('product_id');
                                                    if ($productId) {
                                                        $product = Product::find($productId);
                                                        if ($product && intval($value) > $product->stok) {
                                                            $fail("Jumlah melebihi stok yang tersedia ({$product->stok} unit)");
                                                        }
                                                    }
                                                };
                                            })
                                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                                $harga = $get('harga_satuan') ?: 0;
                                                $jumlah = intval($state) ?: 1;
                                                $productId = $get('product_id');
                                                $stokTersedia = $get('stok_tersedia') ?: 0;
                                                
                                                // Validasi stok
                                                if ($productId && $jumlah > $stokTersedia) {
                                                    $product = Product::find($productId);
                                                    if ($product) {
                                                        $set('jumlah', $product->stok);
                                                        $jumlah = $product->stok;
                                                        
                                                        if ($product->stok == 0) {
                                                            Notification::make()
                                                                ->title('Produk Habis!')
                                                                ->body("Produk {$product->nama_produk} sedang habis stok")
                                                                ->danger()
                                                                ->duration(4000)
                                                                ->send();
                                                        } else {
                                                            Notification::make()
                                                                ->title('Stok Terbatas!')
                                                                ->body("Stok {$product->nama_produk} hanya tersedia {$product->stok} unit")
                                                                ->warning()
                                                                ->duration(4000)
                                                                ->send();
                                                        }
                                                    }
                                                }
                                                
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
                                
                                // Hidden fields
                                Forms\Components\Hidden::make('harga_satuan'),
                                Forms\Components\Hidden::make('stok_tersedia'),
                                
                                // Warning jika stok habis
                                Forms\Components\Placeholder::make('stok_warning')
                                    ->label('')
                                    ->content(function (Get $get): string {
                                        $productId = $get('product_id');
                                        if ($productId) {
                                            $product = Product::find($productId);
                                            if ($product && $product->stok <= 0) {
                                                return '❌ Produk ini sedang habis stok!';
                                            }
                                            if ($product && $product->stok <= 5) {
                                                return '⚠️ Stok produk ini tinggal sedikit (' . $product->stok . ' unit)';
                                            }
                                        }
                                        return '';
                                    })
                                    ->extraAttributes(function (Get $get) {
                                        $productId = $get('product_id');
                                        if ($productId) {
                                            $product = Product::find($productId);
                                            if ($product && $product->stok <= 0) {
                                                return ['class' => 'text-red-600 font-semibold'];
                                            }
                                            if ($product && $product->stok <= 5) {
                                                return ['class' => 'text-orange-600 font-semibold'];
                                            }
                                        }
                                        return [];
                                    })
                                    ->visible(function (Get $get): bool {
                                        $productId = $get('product_id');
                                        if ($productId) {
                                            $product = Product::find($productId);
                                            return $product && $product->stok <= 5;
                                        }
                                        return false;
                                    }),
                                
                                Textarea::make('catatan_item')
                                    ->label('Catatan Item')
                                    ->placeholder('Tambahan, kurang pedas, dll...')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->minItems(1)
                            ->maxItems(20)
                            ->itemLabel(fn (array $state): ?string => 
                                !empty($state['product_id']) 
                                    ? Product::find($state['product_id'])?->nama_produk ?? 'Item'
                                    : 'Item Baru'
                            )
                            ->addActionLabel('Tambah Item')
                            ->deleteAction(
                                fn ($action) => $action->requiresConfirmation()
                            )
                            ->collapsed()
                            ->cloneable(),
                    ])
                    ->collapsed(false)
                    ->collapsible(),

                // Payment Section
                Forms\Components\Section::make('Informasi Pembayaran')
                    ->description('Detail pembayaran dan total harga')
                    ->icon('heroicon-m-credit-card')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                // Total Harga Display
                                Forms\Components\Placeholder::make('total_display')
                                    ->label('Total Harga')
                                    ->content(function (Get $get): string {
                                        $total = $get('total_harga') ?: 0;
                                        return 'Rp ' . number_format($total, 0, ',', '.');
                                    })
                                    ->extraAttributes(['class' => 'text-2xl font-bold text-green-600'])
                                    ->columnSpan(2),
                                
                                Select::make('metode_pembayaran')
                                    ->label('Metode Pembayaran')
                                    ->options([
                                        'saldo' => 'Saldo Murid',
                                        'tunai' => 'Tunai',
                                        'transfer' => 'Transfer Bank',
                                    ])
                                    ->required()
                                    ->live()
                                    ->prefixIcon('heroicon-m-credit-card')
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $total = $get('total_harga') ?: 0;
                                        $saldoMurid = $get('saldo_murid') ?: 0;
                                        
                                        if ($state === 'saldo') {
                                            $set('jumlah_bayar', $total);
                                            $kembalian = $saldoMurid >= $total ? ($saldoMurid - $total) : 0;
                                            $set('kembalian', $kembalian);
                                            $set('kembalian_display', $kembalian);
                                        } else {
                                            $set('jumlah_bayar', 0);
                                            $set('kembalian', 0);
                                            $set('kembalian_display', 0);
                                        }
                                    }),

                                Select::make('status_pembayaran')
                                    ->label('Status Pembayaran')
                                    ->options([
                                        'pending' => 'Pending',
                                        'paid' => 'Lunas',
                                        'failed' => 'Gagal',
                                    ])
                                    ->default('pending')
                                    ->required()
                                    ->prefixIcon('heroicon-m-check-circle'),
                            ]),

                        // Conditional payment fields
                        Forms\Components\Grid::make(2)
                            ->schema([
                                TextInput::make('jumlah_bayar')
                                    ->label('Jumlah Bayar')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->live()
                                    ->prefixIcon('heroicon-m-banknotes')
                                    ->visible(fn (Get $get) => $get('metode_pembayaran') !== 'saldo')
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $total = $get('total_harga') ?: 0;
                                        $jumlahBayar = floatval($state) ?: 0;
                                        $kembalian = $jumlahBayar >= $total ? ($jumlahBayar - $total) : 0;
                                        $set('kembalian', $kembalian);
                                        $set('kembalian_display', $kembalian);
                                    }),

                                Forms\Components\Placeholder::make('kembalian_display_field')
                                    ->label('Kembalian')
                                    ->content(function (Get $get): string {
                                        $kembalian = $get('kembalian_display') ?: 0;
                                        return 'Rp ' . number_format($kembalian, 0, ',', '.');
                                    })
                                    ->extraAttributes(function (Get $get) {
                                        $kembalian = $get('kembalian_display') ?: 0;
                                        return $kembalian < 0 
                                            ? ['class' => 'text-lg font-semibold text-red-600']
                                            : ['class' => 'text-lg font-semibold text-blue-600'];
                                    })
                                    ->visible(fn (Get $get) => $get('metode_pembayaran') !== null),
                            ]),

                        // Saldo validation warning
                        Forms\Components\Placeholder::make('saldo_warning')
                            ->label('')
                            ->content(function (Get $get): string {
                                $metodePembayaran = $get('metode_pembayaran');
                                $total = $get('total_harga') ?: 0;
                                $saldoMurid = $get('saldo_murid') ?: 0;
                                
                                if ($metodePembayaran === 'saldo' && $total > $saldoMurid) {
                                    $kurang = $total - $saldoMurid;
                                    return '⚠️ Saldo tidak mencukupi! Kurang Rp ' . number_format($kurang, 0, ',', '.');
                                }
                                return '';
                            })
                            ->extraAttributes(['class' => 'text-red-600 font-semibold'])
                            ->visible(function (Get $get): bool {
                                $metodePembayaran = $get('metode_pembayaran');
                                $total = $get('total_harga') ?: 0;
                                $saldoMurid = $get('saldo_murid') ?: 0;
                                return $metodePembayaran === 'saldo' && $total > $saldoMurid;
                            }),

                        // Hidden fields
                        Forms\Components\Hidden::make('total_harga'),
                        Forms\Components\Hidden::make('kembalian'),
                        Forms\Components\Hidden::make('kembalian_display'),
                    ])
                    ->collapsed(false)
                    ->collapsible(),

                // Additional Information Section
                Forms\Components\Section::make('Informasi Tambahan')
                    ->description('Catatan dan tanggal pesanan')
                    ->icon('heroicon-m-document-text')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                DateTimePicker::make('tanggal_pesanan')
                                    ->label('Tanggal Pesanan')
                                    ->default(now())
                                    ->required()
                                    ->prefixIcon('heroicon-m-calendar'),

                                Select::make('status_pesanan')
                                    ->label('Status Pesanan')
                                    ->options([
                                        'pending' => 'Pending',
                                        'processing' => 'Diproses',
                                        'ready' => 'Siap',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ])
                                    ->default('pending')
                                    ->required()
                                    ->prefixIcon('heroicon-m-clipboard-document-list'),
                            ]),

                        Textarea::make('catatan')
                            ->label('Catatan Pesanan')
                            ->placeholder('Catatan khusus untuk pesanan ini...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nama_pelanggan')
                    ->label('Murid')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('total_harga')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                BadgeColumn::make('status_pesanan')
                    ->label('Status Pesanan')
                    ->colors([
                        'secondary' => 'pending',
                        'warning' => 'processing',
                        'primary' => 'ready',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-cog' => 'processing',
                        'heroicon-o-check-circle' => 'ready',
                        'heroicon-o-hand-thumb-up' => 'completed',
                        'heroicon-o-x-circle' => 'cancelled',
                    ]),

                BadgeColumn::make('status_pembayaran')
                    ->label('Status Bayar')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'paid',
                        'heroicon-o-x-circle' => 'failed',
                    ]),

                TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->badge()
                    ->colors([
                        'primary' => 'saldo',
                        'success' => 'tunai',
                        'warning' => 'transfer',
                    ]),

                TextColumn::make('tanggal_pesanan')
                    ->label('Tanggal')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_pesanan')
                    ->label('Status Pesanan')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Diproses',
                        'ready' => 'Siap',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ]),

                Tables\Filters\SelectFilter::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Lunas',
                        'failed' => 'Gagal',
                    ]),

                Tables\Filters\SelectFilter::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->options([
                        'saldo' => 'Saldo',
                        'tunai' => 'Tunai',
                        'transfer' => 'Transfer',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}