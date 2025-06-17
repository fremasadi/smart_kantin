<div class="space-y-4">
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Pesanan:</span>
                <span class="ml-2 font-bold text-primary-600">{{ $order->nomor_pesanan }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Pelanggan:</span>
                <span class="ml-2">{{ $order->nama_pelanggan }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Total:</span>
                <span class="ml-2 font-bold text-green-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Status:</span>
                <span class="ml-2 px-2 py-1 rounded-full text-xs font-medium
                    {{ $order->status_pesanan === 'selesai' ? 'bg-green-100 text-green-800' : 
                       ($order->status_pesanan === 'diproses' ? 'bg-blue-100 text-blue-800' : 
                       ($order->status_pesanan === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                    {{ ucfirst($order->status_pesanan) }}
                </span>
            </div>
        </div>
    </div>

    <div class="overflow-hidden bg-white dark:bg-gray-900 shadow-sm rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Produk
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Jumlah
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Harga Satuan
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Subtotal
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Catatan
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($order->orderItems as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($item->product->gambar)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($item->product->gambar) }}" alt="{{ $item->product->nama_produk }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">
                                            {{ substr($item->product->nama_produk, 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $item->product->nama_produk }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item->product->kategori }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $item->jumlah }} pcs
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100">
                        Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900 dark:text-gray-100">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                        {{ $item->catatan_item ?: '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <div class="flex justify-between items-center">
            <span class="text-lg font-medium text-gray-700 dark:text-gray-300">Total Pesanan:</span>
            <span class="text-2xl font-bold text-green-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
        </div>
        @if($order->jumlah_bayar)
        <div class="mt-2 grid grid-cols-2 gap-4 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Jumlah Bayar:</span>
                <span class="font-medium">Rp {{ number_format($order->jumlah_bayar, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Kembalian:</span>
                <span class="font-medium text-green-600">Rp {{ number_format($order->kembalian, 0, ',', '.') }}</span>
            </div>
        </div>
        @endif
    </div>
</div>