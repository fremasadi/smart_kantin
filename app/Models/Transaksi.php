<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = [
        'murid_id',
        'orangtua_id',
        'nominal',
        'bukti_transfer',
        'status',

    ];

   // Transaksi.php
public function murid()
{
    return $this->belongsTo(Murid::class);
}

public function orangtua()
{
    return $this->belongsTo(User::class, 'orangtua_id');
}


    

    
}
