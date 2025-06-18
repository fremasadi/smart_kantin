@extends('layouts.supplier')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Edit Produk</h1>

<form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('supplier.products._form', ['submit' => 'Update'])
</form>
@endsection
