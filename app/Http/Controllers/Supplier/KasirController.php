<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Murid;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        $products = Product::aktif()->get();
        $murids = Murid::all();
        
        return view('supplier.kasir.index', compact('products', 'murids'));
    }
    
    public function getMuridSaldo(Request $request)
    {
        $murid = Murid::where('name', $request->name)->first();
        
        if ($murid) {
            return response()->json([
                'saldo' => $murid->saldo,
                'info' => "{$murid->name} - Kelas {$murid->kelas} (Saldo: Rp " . number_format($murid->saldo, 0, ',', '.') . ")"
            ]);
        }
        
        return response()->json(['saldo' => 0]);
    }
    
    public function getProductInfo(Request $request)
    {
        $product = Product::find($request->product_id);
        
        if ($product) {
            return response()->json([
                'harga' => $product->harga,
                'stok' => $product->stok,
                'nama' => $product->nama_produk
            ]);
        }
        
        return response()->json(['harga' => 0, 'stok' => 0]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'jenis_pelanggan' => 'required|in:murid,guru,staff',
            'nama_pelanggan' => 'required|string',
            'metode_pembayaran' => 'required|in:tunai,saldo',
            'jumlah_bayar' => 'required|numeric|min:0',
            'total_harga' => 'required|numeric|min:0',
            'orderItems' => 'required|array|min:1',
            'orderItems.*.product_id' => 'required|exists:products,id',
            'orderItems.*.jumlah' => 'required|integer|min:1',
            'orderItems.*.harga_satuan' => 'required|numeric|min:0',
            'orderItems.*.subtotal' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Validasi stok
            foreach ($request->orderItems as $item) {
                $product = Product::find($item['product_id']);
                if ($product->stok < $item['jumlah']) {
                    return back()->withErrors(['error' => "Stok {$product->nama_produk} tidak mencukupi!"]);
                }
            }
            
            // Validasi saldo jika menggunakan saldo murid
            if ($request->metode_pembayaran === 'saldo' && $request->jenis_pelanggan === 'murid') {
                $murid = Murid::where('name', $request->nama_pelanggan)->first();
                if (!$murid || $murid->saldo < $request->total_harga) {
                    return back()->withErrors(['error' => 'Saldo murid tidak mencukupi!']);
                }
            }
            
            // Buat order
            $order = Order::create([
                'jenis_pelanggan' => $request->jenis_pelanggan,
                'nama_pelanggan' => $request->nama_pelanggan,
                'total_harga' => $request->total_harga,
                'metode_pembayaran' => $request->metode_pembayaran,
                'jumlah_bayar' => $request->jumlah_bayar,
                'kembalian' => $request->kembalian ?? 0,
                'tanggal_order' => now(),
                'status' => 'completed'
            ]);
            
            // Buat order items dan update stok
            foreach ($request->orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal'],
                    'catatan_item' => $item['catatan_item'] ?? null
                ]);
                
                // Update stok produk
                $product = Product::find($item['product_id']);
                $product->decrement('stok', $item['jumlah']);
            }
            
            // Update saldo murid jika menggunakan saldo
            if ($request->metode_pembayaran === 'saldo' && $request->jenis_pelanggan === 'murid') {
                $murid = Murid::where('name', $request->nama_pelanggan)->first();
                $murid->decrement('saldo', $request->total_harga);
            }
            
            DB::commit();
            
            return redirect()->route('kasir.index')->with('success', 'Pesanan berhasil dibuat!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}