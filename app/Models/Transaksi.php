<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'harga_supplier',
        'harga_jual',
        'jumlah',
        'laba_per_item',
        'total_laba',
    ];

    protected $casts = [
        'harga_supplier' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'laba_per_item' => 'decimal:2',
        'total_laba' => 'decimal:2',
        'jumlah' => 'integer',
    ];

    // Di Transaksi.php
public function orderItem()
{
    return $this->belongsTo(OrderItem::class);
}

// Di OrderItem.php
public function order()
{
    return $this->belongsTo(Order::class);
}

}
