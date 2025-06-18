<?php

namespace App\Exports;

use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SupplierOrderExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $supplierId = Auth::id();

        return OrderItem::with(['order', 'product'])
            ->whereHas('product', fn ($q) => $q->where('supplier_id', $supplierId))
            ->get()
            ->map(function ($item) {
                return [
                    'Nomor Pesanan'     => $item->order->nomor_pesanan,
                    'Nama Pelanggan'    => $item->order->nama_pelanggan,
                    'Produk'            => $item->product->nama_produk,
                    'Jumlah'            => $item->jumlah,
                    'Harga Satuan'      => $item->harga_satuan,
                    'Subtotal'          => $item->subtotal,
                    'Tanggal Pesanan'   => $item->order->tanggal_pesanan->format('Y-m-d H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Nomor Pesanan',
            'Nama Pelanggan',
            'Produk',
            'Jumlah',
            'Harga Satuan',
            'Subtotal',
            'Tanggal Pesanan',
        ];
    }
}
