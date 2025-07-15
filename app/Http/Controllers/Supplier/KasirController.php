<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Murid;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KasirController extends Controller
{
    public function index()
    {
        return view('supplier.kasir.index');
    }

    public function getProducts()
    {
        $products = Product::aktif()->get()->keyBy('id');

        return response()->json([
            'products' => $products
        ]);
    }

    public function getCustomers()
    {
        $customers = [
            'murid' => [],
            'guru' => [],
            'staff' => []
        ];

        // Load murid dengan saldo
        $murid = Murid::all();
        foreach ($murid as $m) {
            $customers['murid'][] = [
                'id' => $m->id,
                'name' => $m->name,
                'display_name' => "{$m->name} - Kelas {$m->kelas} (Saldo: Rp " . number_format($m->saldo, 0, ',', '.') . ")",
                'saldo' => $m->saldo,
                'kelas' => $m->kelas
            ];
        }

        // Untuk guru dan staff, input manual (tanpa data dari database)
        return response()->json([
            'customers' => $customers
        ]);
    }

    public function getProdukPopuler()
    {
        $products = Product::aktif()
            ->orderBy('nama_produk')
            ->take(5)
            ->get();

        return response()->json([
            'products' => $products
        ]);
    }

    public function getTransaksiTerakhir()
    {
        $transactions = Order::with('orderItems.product')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'transactions' => $transactions
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_pelanggan' => 'required|in:murid,guru,staff',
            'nama_pelanggan' => 'required|string',
            'metode_pembayaran' => 'required|in:tunai,saldo',
            'jumlah_bayar' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Validasi pembayaran guru dan staff hanya bisa tunai
            if (in_array($request->jenis_pelanggan, ['guru', 'staff']) && $request->metode_pembayaran !== 'tunai') {
                throw new \Exception('Guru dan Staff hanya bisa melakukan pembayaran tunai');
            }

            // Hitung total
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['subtotal'];
            }

            // Validasi saldo jika menggunakan saldo murid
            if ($request->metode_pembayaran === 'saldo' && $request->jenis_pelanggan === 'murid') {
                $murid = Murid::where('name', $request->nama_pelanggan)->first();
                if (!$murid) {
                    throw new \Exception('Murid tidak ditemukan');
                }

                if ($murid->saldo < $total) {
                    throw new \Exception('Saldo tidak mencukupi');
                }
            }

            // Cek stok produk
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception('Produk tidak ditemukan');
                }

                if ($product->stok < $item['jumlah']) {
                    throw new \Exception("Stok {$product->nama_produk} tidak mencukupi");
                }
            }

            // Buat order (tanpa user_id)
            $order = Order::create([
                'jenis_pelanggan' => $request->jenis_pelanggan,
                'nama_pelanggan' => $request->nama_pelanggan,
                'total_harga' => $total,
                'metode_pembayaran' => $request->metode_pembayaran,
                'jumlah_bayar' => $request->jumlah_bayar,
                'kembalian' => $request->jumlah_bayar - $total,
                'status' => 'selesai',
                'catatan' => $request->catatan ?? null
            ]);

            // Simpan order items dan update stok
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                // Buat order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $product->harga,
                    'subtotal' => $item['subtotal'],
                    'catatan_item' => $item['catatan_item'] ?? null
                ]);

                // Update stok produk
                $product->decrement('stok', $item['jumlah']);
            }

            // Update saldo murid jika menggunakan saldo
            if ($request->metode_pembayaran === 'saldo' && $request->jenis_pelanggan === 'murid') {
                $murid = Murid::where('name', $request->nama_pelanggan)->first();
                $murid->decrement('saldo', $total);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan',
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $order = Order::with('orderItems.product')->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    public function printReceipt($id)
    {
        $order = Order::with('orderItems.product')->find($id);

        if (!$order) {
            abort(404, 'Order tidak ditemukan');
        }

        return view('supplier.kasir.receipt', compact('order'));
    }
}