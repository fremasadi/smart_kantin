<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
        'catatan_item'
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transaksi()
{
    return $this->hasOne(Transaksi::class);
}
protected static function booted()
{
    static::created(function ($item) {
        $product = $item->product;

        $hargaSupplier = $product->harga_supplier;
        $hargaJual = $item->harga_satuan;
        $jumlah = $item->jumlah;
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
    });
}


}