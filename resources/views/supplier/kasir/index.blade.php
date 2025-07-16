@extends('layouts.supplier')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                <span class="text-blue-600">💳</span> Kasir - Buat Pesanan
            </h1>
            <div class="text-sm text-gray-500">
                {{ date('d M Y, H:i') }}
            </div>
        </div>
        
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">Terjadi kesalahan:</span>
                </div>
                <ul class="ml-7 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        
        <form action="{{ route('kasir.store') }}" method="POST" id="kasirForm" class="space-y-8">
            @csrf
            
            <!-- Customer Information Section -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200 p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-800 flex items-center">
                    <span class="text-2xl mr-3">👤</span>
                    Informasi Pelanggan
                </h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Pelanggan <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_pelanggan" id="jenis_pelanggan" 
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                required>
                            <option value="">Pilih jenis pelanggan...</option>
                            <option value="murid">🎓 Murid</option>
                            <option value="guru">👨‍🏫 Guru</option>
                            <option value="staff">👥 Staff</option>
                        </select>
                    </div>
                    
                    <!-- Student Section -->
                    <div id="murid_section" class="customer-section" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Murid <span class="text-red-500">*</span>
                        </label>
                        <select name="nama_pelanggan_murid" id="nama_murid" 
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="">Pilih murid...</option>
                            @foreach($murids as $murid)
                                <option value="{{ $murid->name }}" data-saldo="{{ $murid->saldo }}">
                                    {{ $murid->name }} - Kelas {{ $murid->kelas }}
                                    (Saldo: Rp {{ number_format($murid->saldo, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        <div id="saldo_display" class="mt-3 p-3 bg-blue-100 rounded-lg text-blue-800 font-medium" style="display: none;">
                            <span class="text-blue-600">💳</span> <span id="saldo_text"></span>
                        </div>
                    </div>
                    
                    <!-- Teacher Section -->
                    <div id="guru_section" class="customer-section" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Guru <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_pelanggan_guru" id="nama_guru" 
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="Masukkan nama guru...">
                    </div>
                    
                    <!-- Staff Section -->
                    <div id="staff_section" class="customer-section" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Staff <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_pelanggan_staff" id="nama_staff" 
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               placeholder="Masukkan nama staff...">
                    </div>
                </div>
                
                <input type="hidden" name="nama_pelanggan" id="nama_pelanggan">
                <input type="hidden" name="saldo_murid" id="saldo_murid" value="0">
            </div>
            
            <!-- Order Items Section -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <span class="text-2xl mr-3">🛒</span>
                        Daftar Pesanan
                    </h2>
                    <button type="button" id="add_item" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah Item
                    </button>
                </div>
                
                <div id="order_items" class="space-y-4">
                    <div class="order-item bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-4">
                            <div class="lg:col-span-5">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Produk <span class="text-red-500">*</span>
                                </label>
                                <select name="orderItems[0][product_id]" 
                                        class="product-select w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                        required>
                                    <option value="">Pilih produk...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                                data-harga="{{ $product->harga }}" 
                                                data-stok="{{ $product->stok }}"
                                                {{ $product->stok <= 0 ? 'disabled' : '' }}>
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
                            
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Qty <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="orderItems[0][jumlah]" 
                                       class="jumlah-input w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                       min="1" value="1" required>
                                <div class="stok-hint text-xs mt-1"></div>
                            </div>
                            
                            <div class="lg:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subtotal</label>
                                <input type="text" 
                                       class="subtotal-display w-full p-3 border border-gray-300 rounded-lg bg-gray-50 font-medium" 
                                       readonly>
                            </div>
                            
                            <div class="lg:col-span-2 flex items-end">
                                <button type="button" 
                                        class="remove-item w-full bg-red-500 hover:bg-red-600 text-white px-3 py-3 rounded-lg transition-colors duration-200 flex items-center justify-center" 
                                        style="display: none;">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Item</label>
                            <textarea name="orderItems[0][catatan_item]" 
                                      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                      rows="2" 
                                      placeholder="Tambahan, kurang pedas, dll..."></textarea>
                        </div>
                        
                        <div class="stok-warning bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg font-medium" style="display: none;">
                            <span class="text-red-600">⚠️</span> <span class="warning-text"></span>
                        </div>
                        
                        <input type="hidden" name="orderItems[0][harga_satuan]" class="harga-satuan">
                        <input type="hidden" name="orderItems[0][subtotal]" class="subtotal-value">
                    </div>
                </div>
            </div>
            
            <!-- Order Summary Section -->
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg border border-yellow-200 p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-800 flex items-center">
                    <span class="text-2xl mr-3">💳</span>
                    Ringkasan Pesanan
                </h2>
                
                <div class="mb-6">
                    <div class="bg-white rounded-lg p-4 border-2 border-blue-200">
                        <div class="text-center">
                            <div class="text-sm text-gray-600 mb-1">TOTAL HARGA</div>
                            <div class="text-4xl font-bold text-blue-600">
                                Rp <span id="total_display">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Metode Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <select name="metode_pembayaran" id="metode_pembayaran" 
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                required>
                            <option value="tunai">💵 Tunai</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah Bayar <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="jumlah_bayar" id="jumlah_bayar" 
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               min="0" step="0.01" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" id="kembalian_label">
                            Kembalian
                        </label>
                        <input type="text" id="kembalian_display" 
                               class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 font-medium" 
                               readonly>
                    </div>
                </div>
                
                <div id="saldo_warning" class="mt-4 bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg font-medium" style="display: none;">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span>Saldo tidak mencukupi untuk pesanan ini!</span>
                    </div>
                </div>
                
                <input type="hidden" name="total_harga" id="total_harga">
                <input type="hidden" name="kembalian" id="kembalian">
            </div>
            
            <div class="flex justify-end pt-6">
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Buat Pesanan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemCount = 1;
    
    // Utility functions
    const formatCurrency = (amount) => new Intl.NumberFormat('id-ID').format(amount);
    const hideAllCustomerSections = () => {
        document.querySelectorAll('.customer-section').forEach(section => {
            section.style.display = 'none';
        });
    };
    
    // Customer type change handler
    document.getElementById('jenis_pelanggan').addEventListener('change', function() {
        const value = this.value;
        
        hideAllCustomerSections();
        
        // Show relevant section
        if (value) {
            document.getElementById(`${value}_section`).style.display = 'block';
        }
        
        // Update payment methods
        updatePaymentMethods();
        updateCustomerName();
        calculateTotal();
    });
    
    // Student selection handler
    document.getElementById('nama_murid').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const saldo = parseFloat(selectedOption.getAttribute('data-saldo')) || 0;
        
        document.getElementById('saldo_murid').value = saldo;
        
        const saldoDisplay = document.getElementById('saldo_display');
        const saldoText = document.getElementById('saldo_text');
        
        if (this.value) {
            saldoText.textContent = `Saldo: Rp ${formatCurrency(saldo)}`;
            saldoDisplay.style.display = 'block';
        } else {
            saldoDisplay.style.display = 'none';
        }
        
        updateCustomerName();
        calculateTotal();
    });
    
    // Teacher/Staff input handlers
    ['guru', 'staff'].forEach(type => {
        document.getElementById(`nama_${type}`).addEventListener('input', updateCustomerName);
    });
    
    function updateCustomerName() {
        const customerType = document.getElementById('jenis_pelanggan').value;
        let name = '';
        
        if (customerType) {
            const inputElement = document.getElementById(`nama_${customerType}`);
            name = inputElement ? inputElement.value : '';
        }
        
        document.getElementById('nama_pelanggan').value = name;
    }
    
    function updatePaymentMethods() {
        const customerType = document.getElementById('jenis_pelanggan').value;
        const paymentSelect = document.getElementById('metode_pembayaran');
        
        paymentSelect.innerHTML = '<option value="tunai">💵 Tunai</option>';
        
        if (customerType === 'murid') {
            paymentSelect.innerHTML += '<option value="saldo">💳 Saldo Murid</option>';
        }
    }
    
    // Product selection handler
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            handleProductSelection(e.target);
        }
    });
    
    function handleProductSelection(selectElement) {
        const item = selectElement.closest('.order-item');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const harga = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
        const stok = parseInt(selectedOption.getAttribute('data-stok')) || 0;
        
        // Update hidden price input
        item.querySelector('.harga-satuan').value = harga;
        
        // Update stock hint
        const stockHint = item.querySelector('.stok-hint');
        const quantityInput = item.querySelector('.jumlah-input');
        const stockWarning = item.querySelector('.stok-warning');
        
        if (stok > 0) {
            stockHint.textContent = `Stok tersedia: ${stok}`;
            stockHint.className = 'text-xs text-green-600 mt-1 font-medium';
            quantityInput.max = stok;
            quantityInput.disabled = false;
            stockWarning.style.display = 'none';
            
            if (parseInt(quantityInput.value) > stok) {
                quantityInput.value = stok;
            }
        } else {
            stockHint.textContent = 'Stok habis';
            stockHint.className = 'text-xs text-red-600 mt-1 font-medium';
            quantityInput.value = 0;
            quantityInput.disabled = true;
            stockWarning.querySelector('.warning-text').textContent = 'Produk ini habis stok!';
            stockWarning.style.display = 'block';
        }
        
        updateItemSubtotal(item);
    }
    
    // Quantity input handler
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('jumlah-input')) {
            handleQuantityChange(e.target);
        }
    });
    
    function handleQuantityChange(input) {
        const item = input.closest('.order-item');
        const productSelect = item.querySelector('.product-select');
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const stok = parseInt(selectedOption.getAttribute('data-stok')) || 0;
        
        if (parseInt(input.value) > stok) {
            input.value = stok;
            showAlert(`Stok tersedia hanya: ${stok}`);
        }
        
        updateItemSubtotal(item);
    }
    
    function updateItemSubtotal(item) {
        const harga = parseFloat(item.querySelector('.harga-satuan').value) || 0;
        const jumlah = parseInt(item.querySelector('.jumlah-input').value) || 0;
        const subtotal = harga * jumlah;
        
        item.querySelector('.subtotal-value').value = subtotal;
        item.querySelector('.subtotal-display').value = `Rp ${formatCurrency(subtotal)}`;
        
        calculateTotal();
    }
    
    function calculateTotal() {
        const subtotalInputs = document.querySelectorAll('.subtotal-value');
        let total = 0;
        
        subtotalInputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        
        document.getElementById('total_harga').value = total;
        document.getElementById('total_display').textContent = formatCurrency(total);
        
        updateChangeAmount();
    }
    
    // Payment method change handler
    document.getElementById('metode_pembayaran').addEventListener('change', function() {
        handlePaymentMethodChange();
    });
    
    function handlePaymentMethodChange() {
        const paymentMethod = document.getElementById('metode_pembayaran').value;
        const customerType = document.getElementById('jenis_pelanggan').value;
        const paymentInput = document.getElementById('jumlah_bayar');
        const changeLabel = document.getElementById('kembalian_label');
        const changeDisplay = document.getElementById('kembalian_display');
        
        if (paymentMethod === 'saldo' && customerType === 'murid') {
            const total = parseFloat(document.getElementById('total_harga').value) || 0;
            paymentInput.value = total;
            paymentInput.disabled = true;
            changeLabel.textContent = 'Sisa Saldo';
            changeDisplay.className = 'w-full p-3 border border-gray-300 rounded-lg bg-gray-50 font-medium text-blue-600';
        } else {
            paymentInput.disabled = false;
            changeLabel.textContent = 'Kembalian';
            changeDisplay.className = 'w-full p-3 border border-gray-300 rounded-lg bg-gray-50 font-medium text-green-600';
        }
        
        updateChangeAmount();
    }
    
    // Payment amount input handler
    document.getElementById('jumlah_bayar').addEventListener('input', updateChangeAmount);
    
    function updateChangeAmount() {
        const total = parseFloat(document.getElementById('total_harga').value) || 0;
        const paymentAmount = parseFloat(document.getElementById('jumlah_bayar').value) || 0;
        const paymentMethod = document.getElementById('metode_pembayaran').value;
        const customerType = document.getElementById('jenis_pelanggan').value;
        
        let changeAmount = 0;
        
        if (paymentMethod === 'saldo' && customerType === 'murid') {
            const studentBalance = parseFloat(document.getElementById('saldo_murid').value) || 0;
            changeAmount = studentBalance >= total ? (studentBalance - total) : 0;
            
            const warning = document.getElementById('saldo_warning');
            warning.style.display = total > studentBalance ? 'block' : 'none';
        } else {
            changeAmount = paymentAmount >= total ? (paymentAmount - total) : 0;
        }
        
        document.getElementById('kembalian').value = changeAmount;
        document.getElementById('kembalian_display').value = `Rp ${formatCurrency(changeAmount)}`;
    }
    
    // Add item functionality
    document.getElementById('add_item').addEventListener('click', function() {
        addNewItem();
    });
    
    function addNewItem() {
        const orderItems = document.getElementById('order_items');
        const newItem = document.querySelector('.order-item').cloneNode(true);
        
        // Update form field names
        newItem.querySelectorAll('input, select, textarea').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace('[0]', `[${itemCount}]`));
            }
            
            // Reset values
            if (input.type !== 'hidden') {
                input.value = input.tagName === 'SELECT' ? '' : (input.type === 'number' ? '1' : '');
            }
        });
        
        // Reset display elements
        newItem.querySelector('.subtotal-display').value = '';
        newItem.querySelector('.stok-hint').textContent = '';
        newItem.querySelector('.stok-warning').style.display = 'none';
        
        orderItems.appendChild(newItem);
        itemCount++;
        
        updateRemoveButtons();
    }
    
    // Remove item functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            e.target.closest('.order-item').remove();
            updateRemoveButtons();
            calculateTotal();
        }
    });
    
    function updateRemoveButtons() {
        const items = document.querySelectorAll('.order-item');
        items.forEach(item => {
            const removeBtn = item.querySelector('.remove-item');
            removeBtn.style.display = items.length > 1 ? 'block' : 'none';
        });
    }
    
    function showAlert(message) {
        // Simple alert replacement - you can customize this
        alert(message);
    }
    
    // Initialize
    updateRemoveButtons();
    updatePaymentMethods();
});
</script>
@endsection