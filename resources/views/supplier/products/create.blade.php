@extends('layouts.supplier')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Tambah Produk</h1>

<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('supplier.products._form', ['submit' => 'Simpan'])
</form>
@endsection
