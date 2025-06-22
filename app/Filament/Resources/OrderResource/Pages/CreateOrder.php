<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Murid;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ambil orderItems dari form state karena menggunakan relationship()
        $formState = $this->form->getState();
        $orderItems = $formState['orderItems'] ?? [];
        
        // Hitung total harga dari order items
        $total = 0;
        if (is_array($orderItems) && !empty($orderItems)) {
            foreach ($orderItems as $item) {
                if (isset($item['subtotal']) && is_numeric($item['subtotal'])) {
                    $total += floatval($item['subtotal']);
                }
            }
        }
        
        // Set total_harga berdasarkan perhitungan aktual
        $data['total_harga'] = $total;
        
        // Validasi untuk pembayaran saldo
        if (isset($data['metode_pembayaran']) && $data['metode_pembayaran'] === 'saldo') {
            $murid = Murid::where('name', $data['nama_pelanggan'])->first();
            
            if (!$murid) {
                // Kirim notifikasi error dan halt
                Notification::make()
                    ->title('Error!')
                    ->body('Murid tidak ditemukan!')
                    ->danger()
                    ->duration(5000)
                    ->send();
                $this->halt();
            }
            
            if ($murid->saldo < $total) {
                // Kirim notifikasi error dan halt
                Notification::make()
                    ->title('Saldo Tidak Mencukupi!')
                    ->body('Saldo saat ini: Rp ' . number_format($murid->saldo, 0, ',', '.') . ', Total pesanan: Rp ' . number_format($total, 0, ',', '.'))
                    ->danger()
                    ->duration(5000)
                    ->send();
                $this->halt();
            }
            
            // Set jumlah_bayar sama dengan total untuk pembayaran saldo
            $data['jumlah_bayar'] = $total;
            // Kembalian akan berupa sisa saldo
            $data['kembalian'] = $murid->saldo - $total;
        } else {
            // Hitung kembalian untuk pembayaran non-saldo
            $jumlahBayar = floatval($data['jumlah_bayar'] ?? 0);
            $kembalian = max(0, $jumlahBayar - $total);
            $data['kembalian'] = $kembalian;
        }
        
        // Remove field yang tidak perlu disimpan
        unset($data['kembalian_display']);
        unset($data['computed_total']);
        unset($data['saldo_murid']);
        
        return $data;
    }

    /**
     * Validasi stok produk sebelum membuat order
     */
    protected function validateProductStock(array $orderItems): bool
    {
        if (empty($orderItems)) {
            Notification::make()
                ->title('Error!')
                ->body('Tidak ada item dalam pesanan!')
                ->danger()
                ->duration(5000)
                ->send();
            $this->halt();
        }

        $insufficientStock = [];
        
        foreach ($orderItems as $item) {
            if (!isset($item['product_id']) || !isset($item['jumlah'])) {
                continue;
            }
            
            $product = Product::find($item['product_id']);
            if (!$product) {
                Notification::make()
                    ->title('Error!')
                    ->body('Produk tidak ditemukan!')
                    ->danger()
                    ->duration(5000)
                    ->send();
                $this->halt();
            }
            
            $requestedQty = intval($item['jumlah']);
            
            if ($product->stok < $requestedQty) {
                $insufficientStock[] = [
                    'nama' => $product->nama_produk,
                    'stok_tersedia' => $product->stok,
                    'diminta' => $requestedQty
                ];
            }
        }
        
        if (!empty($insufficientStock)) {
            $errorMessage = "Stok tidak mencukupi untuk produk berikut:\n";
            foreach ($insufficientStock as $item) {
                $errorMessage .= "• {$item['nama']}: Diminta {$item['diminta']}, Tersedia {$item['stok_tersedia']}\n";
            }
            
            Notification::make()
                ->title('Stok Tidak Mencukupi!')
                ->body($errorMessage)
                ->danger()
                ->duration(8000)
                ->send();
            $this->halt();
        }
        
        return true;
    }

    /**
     * Kurangi stok produk berdasarkan order items
     */
    protected function reduceProductStock(array $orderItems): array
    {
        $stockReductions = [];
        
        foreach ($orderItems as $item) {
            if (!isset($item['product_id']) || !isset($item['jumlah'])) {
                continue;
            }
            
            $product = Product::find($item['product_id']);
            if (!$product) {
                continue;
            }
            
            $requestedQty = intval($item['jumlah']);
            $stockBefore = $product->stok;
            
            // Kurangi stok
            $product->stok -= $requestedQty;
            $product->save();
            
            // Log untuk tracking
            $stockReductions[] = [
                'product_id' => $product->id,
                'nama_produk' => $product->nama_produk,
                'stok_sebelum' => $stockBefore,
                'dikurangi' => $requestedQty,
                'stok_sesudah' => $product->stok
            ];
        }
        
        return $stockReductions;
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Ambil form state untuk mendapatkan orderItems
        $formState = $this->form->getState();
        $orderItems = $formState['orderItems'] ?? [];
        
        // **VALIDASI STOK PRODUK SEBELUM MEMBUAT ORDER**
        $this->validateProductStock($orderItems);
        
        // Double check total calculation
        if (empty($data['total_harga']) || $data['total_harga'] == 0) {
            $total = 0;
            if (is_array($orderItems) && !empty($orderItems)) {
                foreach ($orderItems as $item) {
                    if (isset($item['subtotal']) && is_numeric($item['subtotal'])) {
                        $total += floatval($item['subtotal']);
                    }
                }
            }
            
            $data['total_harga'] = $total;
            
            if ($data['metode_pembayaran'] === 'saldo') {
                $data['jumlah_bayar'] = $total;
                $murid = Murid::where('name', $data['nama_pelanggan'])->first();
                $data['kembalian'] = $murid ? ($murid->saldo - $total) : 0;
            } else {
                $jumlahBayar = floatval($data['jumlah_bayar'] ?? 0);
                $data['kembalian'] = max(0, $jumlahBayar - $total);
            }
        }
        
        // Pisahkan data order (hapus orderItems jika ada)
        $orderData = collect($data)->except(['orderItems'])->toArray();
        
        // Buat order terlebih dahulu
        $order = static::getModel()::create($orderData);
        
        // **KURANGI STOK PRODUK SEBELUM MEMBUAT ORDER ITEMS**
        $stockReductions = $this->reduceProductStock($orderItems);
        
        // Buat order items jika ada
        if (!empty($orderItems) && is_array($orderItems)) {
            foreach ($orderItems as $itemData) {
                // Pastikan ada order_id
                $itemData['order_id'] = $order->id;
                
                // Buat order item menggunakan model
                $order->orderItems()->create($itemData);
            }
        }
        
        // **PENTING: Potong saldo murid jika menggunakan metode pembayaran saldo**
        if ($order->metode_pembayaran === 'saldo') {
            $murid = Murid::where('name', $order->nama_pelanggan)->first();
            
            if ($murid) {
                // Kurangi saldo murid
                $saldoSebelum = $murid->saldo;
                $murid->saldo -= $order->total_harga;
                $murid->save();
                
                // Log untuk debugging (opsional)
                \Log::info('Saldo dipotong', [
                    'murid' => $murid->name,
                    'saldo_sebelum' => $saldoSebelum,
                    'total_order' => $order->total_harga,
                    'saldo_sesudah' => $murid->saldo,
                    'order_id' => $order->id
                ]);
            }
        }
        
        // **LOG PENGURANGAN STOK**
        if (!empty($stockReductions)) {
            \Log::info('Stok produk dikurangi', [
                'order_id' => $order->id,
                'reductions' => $stockReductions
            ]);
        }
        
        // Refresh order untuk memastikan relasi ter-load
        $order->refresh();
        
        return $order;
    }
    
    protected function afterCreate(): void
    {
        $muridInfo = '';
        
        // Tambahkan info saldo jika menggunakan metode saldo
        if ($this->record->metode_pembayaran === 'saldo') {
            $murid = Murid::where('name', $this->record->nama_pelanggan)->first();
            if ($murid) {
                $muridInfo = " | Sisa saldo: Rp " . number_format($murid->saldo, 0, ',', '.');
            }
        }
        
        // Tampilkan notifikasi sukses dengan info stok
        $stockInfo = $this->getStockUpdateInfo();
        
        Notification::make()
            ->title('Order berhasil dibuat!')
            ->body("Order #{$this->record->id} telah berhasil disimpan{$muridInfo}{$stockInfo}")
            ->success()
            ->duration(6000)
            ->send();
            
        // Buka halaman print di tab baru
        $printUrl = route('order.print', $this->record->id);
        $this->js("window.open('{$printUrl}', '_blank', 'width=400,height=650,scrollbars=yes,resizable=yes,menubar=no,toolbar=no')");
    }
    
    /**
     * Generate info tentang update stok untuk notifikasi
     */
    protected function getStockUpdateInfo(): string
    {
        $orderItems = $this->record->orderItems()->with('product')->get();
        
        if ($orderItems->isEmpty()) {
            return '';
        }
        
        $lowStockItems = [];
        $outOfStockItems = [];
        
        foreach ($orderItems as $item) {
            if ($item->product) {
                $currentStock = $item->product->stok;
                
                if ($currentStock <= 0) {
                    $outOfStockItems[] = $item->product->nama_produk;
                } elseif ($currentStock <= 5) { // Threshold untuk stok rendah
                    $lowStockItems[] = "{$item->product->nama_produk} ({$currentStock} tersisa)";
                }
            }
        }
        
        $info = '';
        
        if (!empty($outOfStockItems)) {
            $info .= " | ⚠️ Stok habis: " . implode(', ', $outOfStockItems);
        }
        
        if (!empty($lowStockItems)) {
            $info .= " | ⚠️ Stok rendah: " . implode(', ', $lowStockItems);
        }
        
        return $info;
    }
    
    protected function getRedirectUrl(): string
    {
        // Redirect ke index setelah create
        return $this->getResource()::getUrl('index');
    }
    
    // Override form actions untuk menambahkan tombol print
    protected function getCreateFormActions(): array
    {
        return [
            $this->getCreateAnotherFormAction(),
            $this->getCancelFormAction(),
            Actions\Action::make('create_and_print')
                ->label('Simpan & Print')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->keyBindings(['mod+s'])
                ->action('create'),
            $this->getCreateFormAction(),
        ];
    }
}