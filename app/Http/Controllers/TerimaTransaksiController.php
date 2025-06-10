<?php
namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class TerimaTransaksiController extends Controller
{
    public function terima($id)
    {
        $transaksi = Transaksi::with('murid')->findOrFail($id);

        if ($transaksi->status === 'Diterima') {
            return back()->with('message', 'Transaksi sudah diterima.');
        }

        // Tambahkan saldo ke murid
        $murid = $transaksi->murid;
        $murid->saldo += $transaksi->nominal;
        $murid->save();

        // Ubah status transaksi
        $transaksi->status = 'Diterima';
        $transaksi->save();

        return back()->with('message', 'Transaksi berhasil diterima dan saldo ditambahkan.');
    }
}
