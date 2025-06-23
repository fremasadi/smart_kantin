<?php

namespace App\Observers;

use App\Models\OrderItem;

class OrderItemObserver
{
    public function saved(OrderItem $item)
{
    if ($item->transaksi()->exists()) {
        return;
    }

    $item->loadMissing('product');

    if (!$item->product) return;

    $hargaSupplier = $item->product->harga_supplier ?? 0;
    $hargaJual = $item->harga_satuan ?? 0;
    $jumlah = $item->jumlah ?? 0;

    $labaPerItem = $hargaJual - $hargaSupplier;
    $totalLaba = $labaPerItem * $jumlah;

    \App\Models\Transaksi::create([
        'order_item_id' => $item->id,
        'harga_supplier' => $hargaSupplier,
        'harga_jual' => $hargaJual,
        'jumlah' => $jumlah,
        'laba_per_item' => $labaPerItem,
        'total_laba' => $totalLaba,
    ]);
}

}
