<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * Print order receipt
     */
    public function print(Order $order)
    {
        // Load relationships yang diperlukan
        $order->load(['orderItems.product']);
        
        // Format data untuk print
        $data = [
            'order' => $order,
            'items' => $order->orderItems,
            'total_items' => $order->orderItems->sum('jumlah'),
            'print_date' => now()->format('d/m/Y H:i:s'),
            'company_name' => config('app.name', 'Warung Makan'),
            'company_address' => 'Jl. Raya No. 123, Sumenep',
            'company_phone' => '0823-xxxx-xxxx',
        ];
        
        return view('orders.print', $data);
    }
    
    /**
     * Display a listing of the orders
     */
    public function index()
    {
        $orders = Order::with(['orderItems.product'])
            ->latest()
            ->paginate(10);
            
        return view('orders.index', compact('orders'));
    }
    
    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        return view('orders.create');
    }
    
    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris',
            'total_harga' => 'required|numeric|min:0',
            'jumlah_bayar' => 'required|numeric|min:0',
            'kembalian' => 'required|numeric|min:0',
            'orderItems' => 'required|array|min:1',
            'orderItems.*.product_id' => 'required|exists:products,id',
            'orderItems.*.jumlah' => 'required|integer|min:1',
            'orderItems.*.harga_satuan' => 'required|numeric|min:0',
            'orderItems.*.subtotal' => 'required|numeric|min:0',
            'orderItems.*.catatan_item' => 'nullable|string',
        ]);
        
        // Create order
        $order = Order::create([
            'nama_pelanggan' => $validated['nama_pelanggan'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'total_harga' => $validated['total_harga'],
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'kembalian' => $validated['kembalian'],
            'tanggal_order' => now(),
            'status' => 'pending',
        ]);
        
        // Create order items
        foreach ($validated['orderItems'] as $item) {
            $order->orderItems()->create([
                'product_id' => $item['product_id'],
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $item['harga_satuan'],
                'subtotal' => $item['subtotal'],
                'catatan_item' => $item['catatan_item'] ?? null,
            ]);
        }
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Order berhasil dibuat!');
    }
    
    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load(['orderItems.product']);
        
        return view('orders.show', compact('order'));
    }
    
    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order)
    {
        $order->load(['orderItems.product']);
        
        return view('orders.edit', compact('order'));
    }
    
    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'total_harga' => 'required|numeric|min:0',
            'jumlah_bayar' => 'required|numeric|min:0',
            'kembalian' => 'required|numeric|min:0',
        ]);
        
        $order->update($validated);
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Order berhasil diupdate!');
    }
    
    /**
     * Remove the specified order
     */
    public function destroy(Order $order)
    {
        $order->orderItems()->delete();
        $order->delete();
        
        return redirect()->route('orders.index')
            ->with('success', 'Order berhasil dihapus!');
    }
    
    /**
     * Print receipt as PDF (optional)
     */
    public function printPdf(Order $order)
    {
        $order->load(['orderItems.product']);
        
        $data = [
            'order' => $order,
            'items' => $order->orderItems,
            'total_items' => $order->orderItems->sum('jumlah'),
            'print_date' => now()->format('d/m/Y H:i:s'),
            'company_name' => config('app.name', 'Warung Makan'),
            'company_address' => 'Jl. Raya No. 123, Sumenep',
            'company_phone' => '0823-xxxx-xxxx',
        ];
        
        // Jika menggunakan package seperti dompdf atau tcpdf
        // $pdf = PDF::loadView('orders.print-pdf', $data);
        // return $pdf->download('order-' . $order->id . '.pdf');
        
        // Untuk sementara return view biasa
        return view('orders.print-pdf', $data);
    }
    
    /**
     * Get order summary for dashboard
     */
    public function getSummary()
    {
        $today = now()->startOfDay();
        
        $summary = [
            'today_orders' => Order::whereDate('tanggal_order', $today)->count(),
            'today_revenue' => Order::whereDate('tanggal_order', $today)->sum('total_harga'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::sum('total_harga'),
        ];
        
        return response()->json($summary);
    }
    
    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);
        
        $order->update(['status' => $validated['status']]);
        
        return response()->json([
            'success' => true,
            'message' => 'Status order berhasil diupdate!'
        ]);
    }
    
    /**
     * Get orders for specific date range
     */
    public function getOrdersByDateRange(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);
        
        $orders = Order::with(['orderItems.product'])
            ->whereBetween('tanggal_order', [
                $validated['start_date'],
                $validated['end_date']
            ])
            ->latest()
            ->get();
            
        return response()->json($orders);
    }
}