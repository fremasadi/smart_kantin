<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Murid;
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
                throw new \Exception('Murid tidak ditemukan!');
            }
            
            if ($murid->saldo < $total) {
                throw new \Exception('Saldo murid tidak mencukupi! Saldo saat ini: Rp ' . number_format($murid->saldo, 0, ',', '.') . ', Total pesanan: Rp ' . number_format($total, 0, ',', '.'));
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

    protected function handleRecordCreation(array $data): Model
    {
        // Ambil form state untuk mendapatkan orderItems
        $formState = $this->form->getState();
        $orderItems = $formState['orderItems'] ?? [];
        
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
        
        // Tampilkan notifikasi sukses
        Notification::make()
            ->title('Order berhasil dibuat!')
            ->body("Order #{$this->record->id} telah berhasil disimpan{$muridInfo}")
            ->success()
            ->duration(5000)
            ->send();
            
        // Buka halaman print di tab baru
        $printUrl = route('order.print', $this->record->id);
        $this->js("window.open('{$printUrl}', '_blank', 'width=400,height=650,scrollbars=yes,resizable=yes,menubar=no,toolbar=no')");
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