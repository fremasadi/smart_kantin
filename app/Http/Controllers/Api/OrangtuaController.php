<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Murid;

class OrangtuaController extends Controller
{
    public function getMurid(Request $request)
    {
        $user = $request->user();

        // Pastikan role = orangtua
        if ($user->role !== 'orangtua') {
            return response()->json(['message' => 'Unauthorized. Only orangtua can access this.'], 403);
        }

        // Ambil murid berdasarkan user_id (relasi orangtua)
        $murid = $user->murid;

        if (!$murid) {
            return response()->json(['message' => 'Data murid tidak ditemukan.'], 404);
        }

        return response()->json([
            'murid' => $murid
        ]);
    }
}
