@extends('layouts.supplier')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Produk Saya</h1>

<!-- <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">+ Tambah Produk</a> -->

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Harga Supplier</th>
            {{-- <th>Harga Jual</th> --}}
            <th>Stok</th>
            {{-- <th>Status</th> --}}
            <th>Kategori</th>
            <!-- <th>Aksi</th> -->
        </tr>
    </thead>
    <tbody>
    @foreach($products as $p)
        <tr>
            <td>{{ $p->nama_produk }}</td>
            <td>Rp{{ number_format($p->harga_supplier, 0, ',', '.') }}</td>
            {{-- <td>Rp{{ number_format($p->harga, 0, ',', '.') }}</td> --}}
            <td>{{ $p->stok }}</td>
            {{-- <td>{{ ucfirst($p->status) }}</td> --}}
            <td>{{ $p->kategori }}</td>
            <!-- <td>
                <a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('products.destroy', $p) }}" method="POST" style="display:inline-block">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus produk ini?')">Hapus</button>
                </form>
            </td> -->
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
