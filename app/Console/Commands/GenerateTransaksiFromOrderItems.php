<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderItem;
use App\Models\Transaksi;

class GenerateTransaksiFromOrderItems extends Command
{
    protected $signature = 'transaksi:generate';

    protected $description = 'Generate transaksi dari data order_items yang sudah ada';

    public function handle()
    {
        $items = OrderItem::with('product')->get();
        $generated = 0;

        foreach ($items as $item) {
            // Cek kalau sudah ada transaksi, skip
            if ($item->transaksi()->exists()) {
                continue;
            }

            $product = $item->product;

            if (!$product) {
                $this->warn("OrderItem ID {$item->id} tidak punya product. Lewati.");
                continue;
            }

            $hargaSupplier = $product->harga_supplier ?? 0;
            $hargaJual = $item->harga_satuan ?? 0;
            $jumlah = $item->jumlah ?? 0;
            $labaPerItem = $hargaJual - $hargaSupplier;
            $totalLaba = $labaPerItem * $jumlah;

            Transaksi::create([
                'order_item_id' => $item->id,
                'harga_supplier' => $hargaSupplier,
                'harga_jual' => $hargaJual,
                'jumlah' => $jumlah,
                'laba_per_item' => $labaPerItem,
                'total_laba' => $totalLaba,
            ]);

            $generated++;
        }

        $this->info("Transaksi berhasil digenerate: {$generated}");
    }
}
