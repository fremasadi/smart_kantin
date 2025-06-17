<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pesanan',
        'nama_pelanggan',
        'total_harga',
        'jumlah_bayar',
        'kembalian',
        'metode_pembayaran',
        'catatan',
        'tanggal_pesanan'
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'jumlah_bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
        'tanggal_pesanan' => 'datetime'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateNomorPesanan()
    {
        $prefix = 'SunanAmpel-' . date('Ymd');
        $lastOrder = self::where('nomor_pesanan', 'like', $prefix . '%')
            ->latest()
            ->first();
        
        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->nomor_pesanan, -4));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
{
    parent::boot();
    
    static::creating(function ($order) {
        $order->nomor_pesanan = self::generateNomorPesanan();
        $order->tanggal_pesanan = now();
    });
}

public function print(Order $order)
{
    return view('order.print', compact('order'));
}
}