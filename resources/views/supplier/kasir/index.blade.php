@extends('layouts.supplier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
                💳 Sistem Kasir
            </h1>
            <p class="text-gray-600 text-lg">Buat pesanan dengan mudah dan cepat</p>
        </div>

        <div class="max-w-6xl mx-auto">
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="text-red-500 mr-3">⚠️</div>
                        <div>
                            <h4 class="text-red-800 font-semibold">Terjadi kesalahan:</h4>
                            <ul class="text-red-700 mt-2 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="flex items-center">
                                        <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="text-green-500 mr-3">✅</div>
                        <div class="text-green-800 font-semibold">{{ session('success') }}</div>
                    </div>
                </div>
            @endif
            
            <form action="{{ route('kasir.store') }}" method="POST" id="kasirForm" class="space-y-8">
                @csrf
                
                <!-- Customer Information Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <span class="bg-white bg-opacity-20 rounded-full p-2 mr-3">👤</span>
                            Informasi Pelanggan
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Customer Type -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Jenis Pelanggan</label>
                                <select name="jenis_pelanggan" id="jenis_pelanggan" class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" required>
                                    <option value="">Pilih jenis pelanggan...</option>
                                    <option value="murid">🎓 Murid</option>
                                    <option value="guru">👨‍🏫 Guru</option>
                                    <option value="staff">👥 Staff</option>
                                </select>
                            </div>
                            
                            <!-- Student Section -->
                            <div id="murid_section" class="space-y-2" style="display: none;">
                                <label class="block text-sm font-semibold text-gray-700">Nama Murid</label>
                                <select name="nama_pelanggan_murid" id="nama_murid" class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                                    <option value="">Pilih murid...</option>
                                    @foreach($murids as $murid)
                                        <option value="{{ $murid->name }}" data-saldo="{{ $murid->saldo }}">
                                            {{ $murid->name }} - Kelas {{ $murid->kelas }} (Saldo: Rp {{ number_format($murid->saldo, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                <div id="saldo_display" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 font-semibold" style="display: none;">
                                    <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                                    <span id="saldo_text"></span>
                                </div>
                            </div>
                            
                            <!-- Teacher Section -->
                            <div id="guru_section" class="space-y-2" style="display: none;">
                                <label class="block text-sm font-semibold text-gray-700">Nama Guru</label>
                                <input type="text" name="nama_pelanggan_guru" id="nama_guru" class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" placeholder="Masukkan nama guru...">
                            </div>
                            
                            <!-- Staff Section -->
                            <div id="staff_section" class="space-y-2" style="display: none;">
                                <label class="block text-sm font-semibold text-gray-700">Nama Staff</label>
                                <input type="text" name="nama_pelanggan_staff" id="nama_staff" class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200" placeholder="Masukkan nama staff...">
                            </div>
                        </div>
                        
                        <input type="hidden" name="nama_pelanggan" id="nama_pelanggan">
                        <input type="hidden" name="saldo_murid" id="saldo_murid" value="0">
                    </div>
                </div>
                
                <!-- Order Items Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <span class="bg-white bg-opacity-20 rounded-full p-2 mr-3">🛒</span>
                            Daftar Pesanan
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <div id="order_items" class="space-y-4">
                            <div class="order-item bg-gray-50 rounded-xl p-4 border-2 border-gray-200 hover:border-gray-300 transition-all duration-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                    <!-- Product Selection -->
                                    <div class="lg:col-span-2 space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">Produk</label>
                                        <select name="orderItems[0][product_id]" class="product-select w-full p-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200" required>
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
                                    
                                    <!-- Quantity -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">Jumlah</label>
                                        <input type="number" name="orderItems[0][jumlah]" class="jumlah-input w-full p-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200" min="1" value="1" required>
                                        <div class="stok-hint text-xs font-medium"></div>
                                    </div>
                                    
                                    <!-- Subtotal -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700">Subtotal</label>
                                        <input type="text" class="subtotal-display w-full p-3 border-2 border-gray-200 rounded-xl bg-gray-100 font-semibold text-blue-600" readonly>
                                    </div>
                                </div>
                                
                                <!-- Item Notes -->
                                <div class="space-y-2 mb-4">
                                    <label class="block text-sm font-semibold text-gray-700">Catatan Item</label>
                                    <textarea name="orderItems[0][catatan_item]" class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200" rows="2" placeholder="Tambahan, kurang pedas, dll..."></textarea>
                                </div>
                                
                                <!-- Stock Warning -->
                                <div class="stok-warning bg-red-50 border border-red-200 rounded-lg p-3 text-red-700 font-semibold" style="display: none;">
                                    <span class="inline-block w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                    <span>🚫 Produk ini habis stok!</span>
                                </div>
                                
                                <input type="hidden" name="orderItems[0][harga_satuan]" class="harga-satuan">
                                <input type="hidden" name="orderItems[0][subtotal]" class="subtotal-value">
                                
                                <button type="button" class="remove-item bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 mt-3" style="display: none;">
                                    🗑️ Hapus Item
                                </button>
                            </div>
                        </div>
                        
                        <button type="button" id="add_item" class="mt-6 bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                            ➕ Tambah Item
                        </button>
                    </div>
                </div>
                
                <!-- Payment Summary Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <span class="bg-white bg-opacity-20 rounded-full p-2 mr-3">💳</span>
                            Ringkasan Pembayaran
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <!-- Total Display -->
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl p-6 mb-6 text-center">
                            <div class="text-white text-3xl font-bold">
                                TOTAL HARGA
                            </div>
                            <div class="text-white text-4xl font-bold mt-2">
                                Rp <span id="total_display">0</span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Payment Method -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Metode Pembayaran</label>
                                <select name="metode_pembayaran" id="metode_pembayaran" class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200" required>
                                    <option value="tunai">💵 Tunai</option>
                                </select>
                            </div>
                            
                            <!-- Payment Amount -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Jumlah Bayar</label>
                                <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200" min="0" step="0.01" required>
                            </div>
                            
                            <!-- Change/Remaining Balance -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700" id="kembalian_label">Kembalian</label>
                                <input type="text" id="kembalian_display" class="w-full p-3 border-2 border-gray-200 rounded-xl bg-gray-100 text-green-600 font-bold" readonly>
                            </div>
                        </div>
                        
                        <!-- Balance Warning -->
                        <div id="saldo_warning" class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 font-semibold" style="display: none;">
                            <span class="inline-block w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                            ⚠️ Saldo tidak mencukupi untuk pesanan ini!
                        </div>
                        
                        <input type="hidden" name="total_harga" id="total_harga">
                        <input type="hidden" name="kembalian" id="kembalian">
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-12 py-4 rounded-2xl font-bold text-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        🚀 Buat Pesanan
                    </button>
                </div>
            </form>
        </div>
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
        
        // Show relevant section with animation
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
        document.getElementById('saldo_text').textContent = 'Saldo: Rp ' + new Intl.NumberFormat('id-ID').format(saldo);
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
            item.querySelector('.stok-hint').textContent = stok > 0 ? `✅ Stok tersedia: ${stok}` : '❌ Stok habis';
            item.querySelector('.stok-hint').className = stok > 0 ? 'text-xs font-medium text-green-600' : 'text-xs font-medium text-red-600';
            
            const jumlahInput = item.querySelector('.jumlah-input');
            jumlahInput.max = stok;
            
            if (stok <= 0) {
                jumlahInput.value = 0;
                jumlahInput.disabled = true;
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
            kembalianDisplay.className = 'w-full p-3 border-2 border-gray-200 rounded-xl bg-gray-100 text-blue-600 font-bold';
        } else {
            jumlahBayarInput.disabled = false;
            kembalianLabel.textContent = 'Kembalian';
            kembalianDisplay.className = 'w-full p-3 border-2 border-gray-200 rounded-xl bg-gray-100 text-green-600 font-bold';
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