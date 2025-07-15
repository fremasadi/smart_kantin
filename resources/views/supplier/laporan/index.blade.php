@extends('layouts.supplier')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Laporan Penjualan - Hari Ini</h1>

    <a href="{{ route('supplier.laporan.export') }}" class="btn btn-success mb-3">
        <i class="fas fa-file-excel"></i> Export ke Excel
    </a>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nomor Pesanan</th>
                    <th>Nama Pelanggan</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orderItems as $item)
                    <tr>
                        <td>{{ $item->order->nomor_pesanan }}</td>
                        <td>{{ $item->order->nama_pelanggan }}</td>
                        <td>{{ $item->product->nama_produk }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>{{ $item->order->tanggal_pesanan->format('d-m-Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada penjualan hari ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
