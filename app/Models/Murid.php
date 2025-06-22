<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Murid extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nisn',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'kelas',
        'saldo', // ✅ Tambahkan ini
    ];

    // Optional: Jika ingin tipe saldo otomatis dibaca sebagai float/decimal
    protected $casts = [
        'saldo' => 'decimal:2',
        'tanggal_lahir' => 'date',
    ];
}
