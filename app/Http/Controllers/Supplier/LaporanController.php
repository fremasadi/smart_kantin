<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderItem;

class LaporanController extends Controller
{
    public function index()
    {
        $supplierId = Auth::id();

        $orderItems = OrderItem::with(['order', 'product'])
            ->whereHas('product', function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->orderByDesc('created_at')
            ->get();

        return view('supplier.laporan.index', compact('orderItems'));
    }
}
