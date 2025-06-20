<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderItem;
use App\Exports\SupplierOrderExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;


class LaporanController extends Controller
{
    public function index(Request $request)
{
    $supplierId = Auth::id();

    $query = OrderItem::with(['order', 'product'])
        ->whereHas('product', function ($q) use ($supplierId) {
            $q->where('supplier_id', $supplierId);
        });

    // Filter berdasarkan tanggal
    if ($request->filter === 'hari_ini') {
        $query->whereDate('created_at', now()->toDateString());
    } elseif ($request->filter === 'kemarin') {
        $query->whereDate('created_at', now()->subDay()->toDateString());
    } elseif ($request->filter === 'custom') {
        if ($request->filled(['dari', 'sampai'])) {
            $query->whereBetween('created_at', [$request->dari, $request->sampai]);
        }
    }

    $orderItems = $query->orderByDesc('created_at')->get();

    return view('supplier.laporan.index', compact('orderItems'));
}


    public function export()
{
    return Excel::download(new SupplierOrderExport, 'laporan_penjualan.xlsx');
}

}
