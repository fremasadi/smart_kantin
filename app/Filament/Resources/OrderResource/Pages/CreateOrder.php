<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
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
        
        // Hitung kembalian
        $jumlahBayar = floatval($data['jumlah_bayar'] ?? 0);
        $kembalian = max(0, $jumlahBayar - $total);
        $data['kembalian'] = $kembalian;
        
        // Remove field yang tidak perlu disimpan
        unset($data['kembalian_display']);
        unset($data['computed_total']);
        
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
            $jumlahBayar = floatval($data['jumlah_bayar'] ?? 0);
            $data['kembalian'] = max(0, $jumlahBayar - $total);
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
        
        // Refresh order untuk memastikan relasi ter-load
        $order->refresh();
        
        return $order;
    }
    
    protected function afterCreate(): void
    {
        // Tampilkan notifikasi sukses
        Notification::make()
            ->title('Order berhasil dibuat!')
            ->body("Order #{$this->record->id} telah berhasil disimpan")
            ->success()
            ->duration(3000)
            ->send();
    }
    
    protected function getRedirectUrl(): string
    {
        // Redirect ke index setelah create
        return $this->getResource()::getUrl('index');
    }
    
    // Override method untuk menambahkan JavaScript
    public function getExtraBodyAttributes(): array
    {
        return [
            'x-data' => '{
                openPrintPage(url) {
                    window.open(url, "_blank", "width=400,height=650,scrollbars=yes,resizable=yes,menubar=no,toolbar=no");
                }
            }'
        ];
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
                ->action(function () {
                    // Simpan order
                    $this->create();
                    
                    // Buka print page
                    $printUrl = route('order.print', $this->record->id);
                    $this->dispatch('open-print-window', ['url' => $printUrl]);
                }),
            $this->getCreateFormAction(),
        ];
    }
}