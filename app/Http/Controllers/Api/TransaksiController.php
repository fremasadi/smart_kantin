<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Storage;

class TransaksiController extends Controller
{
    // Get semua transaksi milik orangtua
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'orangtua') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $transaksi = Transaksi::with('murid')
            ->where('orangtua_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['transaksi' => $transaksi]);
    }

    // Create transaksi baru
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'orangtua') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'murid_id' => 'required|exists:murids,id',
            'nominal' => 'required|numeric|min:1',
            'bukti_transfer' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('bukti_transfer')) {
            $path = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
        }

        $transaksi = Transaksi::create([
            'murid_id' => $request->murid_id,
            'orangtua_id' => $user->id,
            'nominal' => $request->nominal,
            'bukti_transfer' => $path,
        ]);

        return response()->json([
            'message' => 'Transaksi berhasil dibuat',
            'transaksi' => $transaksi,
        ], 201);
    }
}
