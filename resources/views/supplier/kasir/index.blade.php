@extends('layouts.supplier')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Kasir - Buat Pesanan</h1>
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        
        <form action="{{ route('kasir.store') }}" method="POST" id="kasirForm">
            @csrf
            
            <!-- Informasi Pelanggan -->
            <div class="mb-8 p-4 border rounded-lg bg-gray-50">
                <h2 class="text-lg font-semibold mb-4 text-gray-700">👤 Informasi Pelanggan</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pelanggan</label>
                        <select name="jenis_pelanggan" id="jenis_pelanggan" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Pilih jenis pelanggan...</option>
                            <option value="murid">🎓 Murid</option>
                            <option value="guru">👨‍🏫 Guru</option>
                            <option value="staff">👥 Staff</option>
                        </select>
                    </div>
                    
                    <!-- Nama Murid -->
                    <div id="murid_section" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Murid</label>
                        <select name="nama_pelanggan_murid" id="nama_murid" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih murid...</option>
                            @foreach($murids as $murid)
                                <option value="{{ $murid->name }}" data-saldo="{{ $murid->saldo }}">
                                    {{ $murid->name }} - Kelas {{ $murid->kelas }} (Saldo: Rp {{ number_format($murid->saldo, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        <div id="saldo_display" class="mt-2 text-blue-600 font-semibold" style="display: none;"></div>
                    </div>
                    
                    <!-- Nama Guru -->
                    <div id="guru_section" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Guru</label>
                        <input type="text" name="nama_pelanggan_guru" id="nama_guru" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nama guru...">
                    </div>
                    
                    <!-- Nama Staff -->
                    <div id="staff_section" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Staff</label>
                        <input type="text" name="nama_pelanggan_staff" id="nama_staff" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nama staff...">
                    </div>
                </div>
                
                <input type="hidden" name="nama_pelanggan" id="nama_pelanggan">
                <input type="hidden" name="saldo_murid" id="saldo_murid" value="0">
            </div>
            
            <!-- Daftar Pesanan -->
            <div class="mb-8 p-4 border rounded-lg bg-gray-50">
                <h2 class="text-lg font-semibold mb-4 text-gray-700">🛒 Daftar Pesanan</h2>
                
                <div id="order_items">
                    <div class="order-item mb-4 p-3 border rounded bg-white">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-3">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Produk</label>
                                <select name="orderItems[0][product_id]" class="product-select w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Pilih produk...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-harga="{{ $product->harga }}" data-stok="{{ $product->stok }}">
                                            {{ $product->nama_produk }} - Rp {{ number_format($product->harga, 0, ',', '.') }}
                                            @if($product->stok > 0)
                                                (Stok: {{ $product->stok }})
                                            @else
                                                (HABIS)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Qty</label>
                                <input type="number" name="orderItems[0][jumlah]" class="jumlah-input w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" min="1" value="1" required>
                                <div class="stok-hint text-xs text-gray-500 mt-1"></div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                                <input type="text" class="subtotal-display w-full p-2 border border-gray-300 rounded-md bg-gray-100" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Item</label>
                            <textarea name="orderItems[0][catatan_item]" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" rows="2" placeholder="Tambahan, kurang pedas, dll..."></textarea>
                        </div>
                        
                        <div class="stok-warning text-red-600 font-semibold" style="display: none;"></div>
                        
                        <input type="hidden" name="orderItems[0][harga_satuan]" class="harga-satuan">
                        <input type="hidden" name="orderItems[0][subtotal]" class="subtotal-value">
                        
                        <button type="button" class="remove-item bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 mt-2" style="display: none;">Hapus Item</button>
                    </div>
                </div>
                
                <button type="button" id="add_item" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">+ Tambah Item</button>
            </div>
            
            <!-- Ringkasan Pesanan -->
            <div class="mb-8 p-4 border rounded-lg bg-gray-50">
                <h2 class="text-lg font-semibold mb-4 text-gray-700">💳 Ringkasan Pesanan</h2>
                
                <div class="mb-4">
                    <div class="text-2xl font-bold text-blue-600">
                        TOTAL HARGA: Rp <span id="total_display">0</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
                            <option value="tunai">💵 Tunai</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Bayar</label>
                        <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" min="0" step="0.01" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" id="kembalian_label">Kembalian</label>
                        <input type="text" id="kembalian_display" class="w-full p-2 border border-gray-300 rounded-md bg-gray-100 text-green-600 font-semibold" readonly>
                    </div>
                </div>
                
                <div id="saldo_warning" class="text-red-600 font-semibold mt-2" style="display: none;">
                    ⚠️ Saldo tidak mencukupi untuk pesanan ini!
                </div>
                
                <input type="hidden" name="total_harga" id="total_harga">
                <input type="hidden" name="kembalian" id="kembalian">
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 font-semibold">
                    Buat Pesanan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemCount = 1;
    
    // Handle jenis pelanggan change
    document.getElementById('jenis_pelanggan').addEventListener('change', function() {
        const value = this.value;
        
        // Hide all sections
        document.getElementById('murid_section').style.display = 'none';
        document.getElementById('guru_section').style.display = 'none';
        document.getElementById('staff_section').style.display = 'none';
        
        // Show relevant section
        if (value === 'murid') {
            document.getElementById('murid_section').style.display = 'block';
            updateMetodePembayaran();
        } else if (value === 'guru') {
            document.getElementById('guru_section').style.display = 'block';
        } else if (value === 'staff') {
            document.getElementById('staff_section').style.display = 'block';
        }
        
        // Reset payment method
        document.getElementById('metode_pembayaran').innerHTML = '<option value="tunai">💵 Tunai</option>';
        if (value === 'murid') {
            document.getElementById('metode_pembayaran').innerHTML += '<option value="saldo">💳 Saldo Murid</option>';
        }
        
        updateNamaPelanggan();
        calculateTotal();
    });
    
    // Handle murid selection
    document.getElementById('nama_murid').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const saldo = selectedOption.getAttribute('data-saldo') || 0;
        
        document.getElementById('saldo_murid').value = saldo;
        document.getElementById('saldo_display').textContent = 'Saldo: Rp ' + new Intl.NumberFormat('id-ID').format(saldo);
        document.getElementById('saldo_display').style.display = this.value ? 'block' : 'none';
        
        updateNamaPelanggan();
        calculateTotal();
    });
    
    // Handle guru/staff input
    document.getElementById('nama_guru').addEventListener('input', updateNamaPelanggan);
    document.getElementById('nama_staff').addEventListener('input', updateNamaPelanggan);
    
    function updateNamaPelanggan() {
        const jenisPelanggan = document.getElementById('jenis_pelanggan').value;
        let nama = '';
        
        if (jenisPelanggan === 'murid') {
            nama = document.getElementById('nama_murid').value;
        } else if (jenisPelanggan === 'guru') {
            nama = document.getElementById('nama_guru').value;
        } else if (jenisPelanggan === 'staff') {
            nama = document.getElementById('nama_staff').value;
        }
        
        document.getElementById('nama_pelanggan').value = nama;
    }
    
    function updateMetodePembayaran() {
        const jenisPelanggan = document.getElementById('jenis_pelanggan').value;
        const metodePembayaran = document.getElementById('metode_pembayaran');
        
        metodePembayaran.innerHTML = '<option value="tunai">💵 Tunai</option>';
        
        if (jenisPelanggan === 'murid') {
            metodePembayaran.innerHTML += '<option value="saldo">💳 Saldo Murid</option>';
        }
    }
    
    // Handle product selection
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const item = e.target.closest('.order-item');
            const selectedOption = e.target.options[e.target.selectedIndex];
            const harga = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
            const stok = parseInt(selectedOption.getAttribute('data-stok')) || 0;
            
            item.querySelector('.harga-satuan').value = harga;
            item.querySelector('.stok-hint').textContent = stok > 0 ? `Stok tersedia: ${stok}` : 'Stok habis';
            item.querySelector('.stok-hint').className = stok > 0 ? 'text-xs text-green-600 mt-1' : 'text-xs text-red-600 mt-1';
            
            const jumlahInput = item.querySelector('.jumlah-input');
            jumlahInput.max = stok;
            
            if (stok <= 0) {
                jumlahInput.value = 0;
                jumlahInput.disabled = true;
                item.querySelector('.stok-warning').textContent = '🚫 Produk ini habis stok!';
                item.querySelector('.stok-warning').style.display = 'block';
            } else {
                jumlahInput.disabled = false;
                item.querySelector('.stok-warning').style.display = 'none';
                if (jumlahInput.value > stok) {
                    jumlahInput.value = stok;
                }
            }
            
            updateSubtotal(item);
        }
    });
    
    // Handle quantity change
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('jumlah-input')) {
            const item = e.target.closest('.order-item');
            const productSelect = item.querySelector('.product-select');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const stok = parseInt(selectedOption.getAttribute('data-stok')) || 0;
            
            if (parseInt(e.target.value) > stok) {
                e.target.value = stok;
                alert(`Stok tersedia hanya: ${stok}`);
            }
            
            updateSubtotal(item);
        }
    });
    
    function updateSubtotal(item) {
        const harga = parseFloat(item.querySelector('.harga-satuan').value) || 0;
        const jumlah = parseInt(item.querySelector('.jumlah-input').value) || 0;
        const subtotal = harga * jumlah;
        
        item.querySelector('.subtotal-value').value = subtotal;
        item.querySelector('.subtotal-display').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
        
        calculateTotal();
    }
    
    function calculateTotal() {
        const subtotalInputs = document.querySelectorAll('.subtotal-value');
        let total = 0;
        
        subtotalInputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        
        document.getElementById('total_harga').value = total;
        document.getElementById('total_display').textContent = new Intl.NumberFormat('id-ID').format(total);
        
        updateKembalian();
    }
    
    // Handle payment method change
    document.getElementById('metode_pembayaran').addEventListener('change', function() {
        const jenisPelanggan = document.getElementById('jenis_pelanggan').value;
        const metodePembayaran = this.value;
        const jumlahBayarInput = document.getElementById('jumlah_bayar');
        const kembalianLabel = document.getElementById('kembalian_label');
        const kembalianDisplay = document.getElementById('kembalian_display');
        
        if (metodePembayaran === 'saldo' && jenisPelanggan === 'murid') {
            const total = parseFloat(document.getElementById('total_harga').value) || 0;
            jumlahBayarInput.value = total;
            jumlahBayarInput.disabled = true;
            kembalianLabel.textContent = 'Sisa Saldo';
            kembalianDisplay.className = 'w-full p-2 border border-gray-300 rounded-md bg-gray-100 text-blue-600 font-semibold';
        } else {
            jumlahBayarInput.disabled = false;
            kembalianLabel.textContent = 'Kembalian';
            kembalianDisplay.className = 'w-full p-2 border border-gray-300 rounded-md bg-gray-100 text-green-600 font-semibold';
        }
        
        updateKembalian();
    });
    
    // Handle jumlah bayar change
    document.getElementById('jumlah_bayar').addEventListener('input', updateKembalian);
    
    function updateKembalian() {
        const total = parseFloat(document.getElementById('total_harga').value) || 0;
        const jumlahBayar = parseFloat(document.getElementById('jumlah_bayar').value) || 0;
        const metodePembayaran = document.getElementById('metode_pembayaran').value;
        const jenisPelanggan = document.getElementById('jenis_pelanggan').value;
        
        let kembalian = 0;
        
        if (metodePembayaran === 'saldo' && jenisPelanggan === 'murid') {
            const saldoMurid = parseFloat(document.getElementById('saldo_murid').value) || 0;
            kembalian = saldoMurid >= total ? (saldoMurid - total) : 0;
            
            // Show warning if insufficient balance
            const warning = document.getElementById('saldo_warning');
            if (total > saldoMurid) {
                warning.style.display = 'block';
            } else {
                warning.style.display = 'none';
            }
        } else {
            kembalian = jumlahBayar >= total ? (jumlahBayar - total) : 0;
        }
        
        document.getElementById('kembalian').value = kembalian;
        document.getElementById('kembalian_display').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(kembalian);
    }
    
    // Add item functionality
    document.getElementById('add_item').addEventListener('click', function() {
        const orderItems = document.getElementById('order_items');
        const newItem = document.querySelector('.order-item').cloneNode(true);
        
        // Update names and reset values
        newItem.querySelectorAll('input, select, textarea').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace('[0]', `[${itemCount}]`));
            }
            if (input.type !== 'hidden') {
                input.value = '';
            }
        });
        
        // Reset display values
        newItem.querySelector('.subtotal-display').value = '';
        newItem.querySelector('.stok-hint').textContent = '';
        newItem.querySelector('.stok-warning').style.display = 'none';
        
        // Show remove button
        newItem.querySelector('.remove-item').style.display = 'inline-block';
        
        orderItems.appendChild(newItem);
        itemCount++;
        
        // Update remove button display for all items
        updateRemoveButtons();
    });
    
    // Remove item functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.order-item').remove();
            updateRemoveButtons();
            calculateTotal();
        }
    });
    
    function updateRemoveButtons() {
        const items = document.querySelectorAll('.order-item');
        items.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-item');
            if (items.length > 1) {
                removeBtn.style.display = 'inline-block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }
    
    // Initialize
    updateRemoveButtons();
});
</script>
@endsection