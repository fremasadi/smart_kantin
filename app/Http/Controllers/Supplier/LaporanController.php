<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Exports\SupplierOrderExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
{
    $orderItems = OrderItem::with(['order', 'product'])
        ->whereDate('created_at', now()->toDateString())
        ->orderByDesc('created_at')
        ->get();

    return view('supplier.laporan.index', compact('orderItems'));
}


    public function export()
    {
        return Excel::download(new SupplierOrderExport, 'laporan_penjualan.xlsx');
    }
}
