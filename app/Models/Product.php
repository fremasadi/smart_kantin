<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // pastikan ini ada di bagian atas

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'harga',
        'harga_supplier', // tambahkan ini
        'stok',
        'kategori',
        'gambar',
        'status',
        'supplier_id', // tambahkan ini

    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function supplier()
{
    return $this->belongsTo(User::class, 'supplier_id');
}
public function returnToSupplier(): void
{
    $this->stok = 0;
    $this->returned_at = now();
    $this->save();
}

}