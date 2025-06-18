<div class="mb-3">
    <label>Nama Produk</label>
    <input type="text" name="nama_produk" class="form-control" value="{{ old('nama_produk', $product->nama_produk ?? '') }}" required>
</div>
<div class="mb-3">
    <label>Deskripsi</label>
    <textarea name="deskripsi" class="form-control">{{ old('deskripsi', $product->deskripsi ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label>Harga</label>
    <input type="number" name="harga" class="form-control" value="{{ old('harga', $product->harga ?? '') }}" required>
</div>
<div class="mb-3">
    <label>Stok</label>
    <input type="number" name="stok" class="form-control" value="{{ old('stok', $product->stok ?? '') }}" required>
</div>
<div class="mb-3">
    <label>Kategori</label>
    <input type="text" name="kategori" class="form-control" value="{{ old('kategori', $product->kategori ?? '') }}" required>
</div>
{{-- <div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control" required>
        <option value="aktif" {{ old('status', $product->status ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="nonaktif" {{ old('status', $product->status ?? '') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
    </select>
</div> --}}
<div class="mb-3">
    <label>Gambar (opsional)</label>
    <input type="file" name="gambar" class="form-control">
    @if(isset($product->gambar))
        <img src="{{ asset('storage/'.$product->gambar) }}" alt="" width="120" class="mt-2">
    @endif
</div>
<button type="submit" class="btn btn-success">{{ $submit }}</button>
