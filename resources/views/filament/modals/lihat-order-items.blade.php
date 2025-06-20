<div class="space-y-2">
    @foreach ($items as $item)
        <div class="border rounded p-3 bg-gray-50">
            <p><strong>Produk:</strong> {{ $item->product->nama_produk }}</p>
            <p><strong>Jumlah:</strong> {{ $item->jumlah }}</p>
            <p><strong>Harga Satuan:</strong> Rp{{ number_format($item->harga_satuan, 0, ',', '.') }}</p>
            <p><strong>Subtotal:</strong> Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
        </div>
    @endforeach
</div>
