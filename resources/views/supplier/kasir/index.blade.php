@extends('layouts.supplier')

@section('content')
<style>
    /* Custom CSS for Kasir Form */
    .kasir-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px 16px;
    }

    .kasir-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        padding: 32px;
        margin-bottom: 24px;
    }

    .kasir-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 32px;
        color: #1f2937;
        text-align: center;
        position: relative;
    }

    .kasir-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: linear-gradient(90deg, #3b82f6, #06b6d4);
        border-radius: 2px;
    }

    /* Alert Styles */
    .alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        border: 1px solid;
    }

    .alert-error {
        background-color: #fef2f2;
        border-color: #fca5a5;
        color: #dc2626;
    }

    .alert-success {
        background-color: #f0fdf4;
        border-color: #86efac;
        color: #16a34a;
    }

    .alert ul {
        margin: 0;
        padding-left: 20px;
    }

    /* Section Styles */
    .section {
        margin-bottom: 40px;
        padding: 24px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        transition: all 0.3s ease;
    }

    .section:hover {
        border-color: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -8px rgba(59, 130, 246, 0.3);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 24px;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title::before {
        content: '';
        width: 4px;
        height: 24px;
        background: linear-gradient(135deg, #3b82f6, #06b6d4);
        border-radius: 2px;
    }

    /* Grid System */
    .grid {
        display: grid;
        gap: 24px;
    }

    .grid-cols-1 {
        grid-template-columns: repeat(1, 1fr);
    }

    .grid-cols-2 {
        grid-template-columns: repeat(2, 1fr);
    }

    .grid-cols-3 {
        grid-template-columns: repeat(3, 1fr);
    }

    .grid-cols-4 {
        grid-template-columns: repeat(4, 1fr);
    }

    @media (max-width: 768px) {
        .grid-cols-2,
        .grid-cols-3,
        .grid-cols-4 {
            grid-template-columns: 1fr;
        }
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 8px;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #d1d5db;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        transform: translateY(-1px);
    }

    .form-input:disabled {
        background-color: #f3f4f6;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .form-textarea {
        resize: vertical;
        min-height: 80px;
    }

    /* Order Item Styles */
    .order-item {
        margin-bottom: 24px;
        padding: 20px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        background: white;
        transition: all 0.3s ease;
        position: relative;
    }

    .order-item:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px -4px rgba(59, 130, 246, 0.2);
    }

    .order-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #06b6d4);
        border-radius: 12px 12px 0 0;
    }

    /* Button Styles */
    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        font-size: 1rem;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.3);
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        font-size: 1.125rem;
        padding: 16px 32px;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #059669, #047857);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        padding: 8px 16px;
        font-size: 0.875rem;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }

    /* Display Elements */
    .total-display {
        font-size: 2rem;
        font-weight: 700;
        color: #059669;
        text-align: center;
        padding: 20px;
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border-radius: 12px;
        margin-bottom: 24px;
        border: 2px solid #10b981;
    }

    .saldo-display {
        margin-top: 8px;
        font-weight: 600;
        color: #0ea5e9;
        padding: 8px 12px;
        background: #f0f9ff;
        border-radius: 6px;
        border: 1px solid #7dd3fc;
    }

    .stok-hint {
        font-size: 0.75rem;
        margin-top: 4px;
        font-weight: 500;
    }

    .stok-hint.available {
        color: #059669;
    }

    .stok-hint.unavailable {
        color: #dc2626;
    }

    .warning {
        color: #dc2626;
        font-weight: 600;
        padding: 12px;
        background: #fef2f2;
        border: 1px solid #fca5a5;
        border-radius: 6px;
        margin-top: 12px;
    }

    .warning.hidden {
        display: none;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .kasir-container {
            padding: 16px 8px;
        }

        .kasir-card {
            padding: 20px;
        }

        .kasir-title {
            font-size: 1.5rem;
        }

        .section {
            padding: 16px;
        }

        .total-display {
            font-size: 1.5rem;
        }

        .btn-success {
            width: 100%;
        }
    }

    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .section {
        animation: fadeIn 0.5s ease-out;
    }

    /* Loading state */
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 32px;
        height: 32px;
        border: 3px solid #f3f4f6;
        border-top: 3px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
</style>

<div class="kasir-container">
    <div class="kasir-card">
        <h1 class="kasir-title">🛍️ Kasir - Buat Pesanan</h1>
        
        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <form action="{{ route('kasir.store') }}" method="POST" id="kasirForm">
            @csrf
            
            <!-- Informasi Pelanggan -->
            <div class="section">
                <h2 class="section-title">👤 Informasi Pelanggan</h2>
                
                <div class="grid grid-cols-2">
                    <div class="form-group">
                        <label class="form-label">Jenis Pelanggan</label>
                        <select name="jenis_pelanggan" id="jenis_pelanggan" class="form-select" required>
                            <option value="">Pilih jenis pelanggan...</option>
                            <option value="murid">🎓 Murid</option>
                            <option value="guru">👨‍🏫 Guru</option>
                            <option value="staff">👥 Staff</option>
                        </select>
                    </div>
                    
                    <!-- Nama Murid -->
                    <div id="murid_section" class="form-group hidden">
                        <label class="form-label">Nama Murid *</label>
                        <select name="nama_pelanggan_murid" id="nama_murid" class="form-select">
                            <option value="">Pilih murid...</option>
                            @foreach($murids as $murid)
                                <option value="{{ $murid->name }}" data-saldo="{{ $murid->saldo }}">
                                    {{ $murid->name }} - Kelas {{ $murid->kelas }} (Saldo: Rp {{ number_format($murid->saldo, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        <div id="saldo_display" class="saldo-display hidden"></div>
                    </div>
                    
                    <!-- Nama Guru -->
                    <div id="guru_section" class="form-group hidden">
                        <label class="form-label">Nama Guru *</label>
                        <input type="text" name="nama_pelanggan_guru" id="nama_guru" class="form-input" placeholder="Masukkan nama guru...">
                    </div>
                    
                    <!-- Nama Staff -->
                    <div id="staff_section" class="form-group hidden">
                        <label class="form-label">Nama Staff *</label>
                        <input type="text" name="nama_pelanggan_staff" id="nama_staff" class="form-input" placeholder="Masukkan nama staff...">
                    </div>
                </div>
                
                <input type="hidden" name="nama_pelanggan" id="nama_pelanggan">
                <input type="hidden" name="saldo_murid" id="saldo_murid" value="0">
            </div>
            
            <!-- Daftar Pesanan -->
            <div class="section">
                <h2 class="section-title">🛒 Daftar Pesanan</h2>
                
                <div id="order_items">
                    <div class="order-item">
                        <div class="grid grid-cols-4">
                            <div class="form-group" style="grid-column: span 2;">
                                <label class="form-label">Produk *</label>
                                <select name="orderItems[0][product_id]" class="product-select form-select" required>
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
                            
                            <div class="form-group">
                                <label class="form-label">Qty *</label>
                                <input type="number" name="orderItems[0][jumlah]" class="jumlah-input form-input" min="1" value="1" required>
                                <div class="stok-hint"></div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Subtotal</label>
                                <input type="text" class="subtotal-display form-input" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Catatan Item</label>
                            <textarea name="orderItems[0][catatan_item]" class="form-textarea" placeholder="Tambahan, kurang pedas, dll..."></textarea>
                        </div>
                        
                        <div class="stok-warning warning hidden"></div>
                        
                        <input type="hidden" name="orderItems[0][harga_satuan]" class="harga-satuan">
                        <input type="hidden" name="orderItems[0][subtotal]" class="subtotal-value">
                        
                        <button type="button" class="remove-item btn btn-danger hidden">🗑️ Hapus Item</button>
                    </div>
                </div>
                
                <button type="button" id="add_item" class="btn btn-primary">➕ Tambah Item</button>
            </div>
            
            <!-- Ringkasan Pesanan -->
            <div class="section">
                <h2 class="section-title">💳 Ringkasan Pesanan</h2>
                
                <div class="total-display">
                    TOTAL HARGA: Rp <span id="total_display">0</span>
                </div>
                
                <div class="grid grid-cols-3">
                    <div class="form-group">
                        <label class="form-label">Metode Pembayaran *</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required>
                            <option value="tunai">💵 Tunai</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Jumlah Bayar *</label>
                        <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-input" min="0" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" id="kembalian_label">Kembalian</label>
                        <input type="text" id="kembalian_display" class="form-input" readonly>
                    </div>
                </div>
                
                <div id="saldo_warning" class="warning hidden">
                    ⚠️ Saldo tidak mencukupi untuk pesanan ini!
                </div>
                
                <input type="hidden" name="total_harga" id="total_harga">
                <input type="hidden" name="kembalian" id="kembalian">
            </div>
            
            <div style="text-align: center;">
                <button type="submit" class="btn btn-success">
                    ✅ Buat Pesanan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemCount = 1;
    
    // Utility functions
    function showElement(element) {
        element.classList.remove('hidden');
    }
    
    function hideElement(element) {
        element.classList.add('hidden');
    }
    
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }
    
    // Handle jenis pelanggan change
    document.getElementById('jenis_pelanggan').addEventListener('change', function() {
        const value = this.value;
        
        // Hide all sections
        hideElement(document.getElementById('murid_section'));
        hideElement(document.getElementById('guru_section'));
        hideElement(document.getElementById('staff_section'));
        
        // Show relevant section with animation
        if (value === 'murid') {
            showElement(document.getElementById('murid_section'));
            updateMetodePembayaran();
        } else if (value === 'guru') {
            showElement(document.getElementById('guru_section'));
        } else if (value === 'staff') {
            showElement(document.getElementById('staff_section'));
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
        const saldoDisplay = document.getElementById('saldo_display');
        saldoDisplay.textContent = '💰 Saldo: Rp ' + formatRupiah(saldo);
        
        if (this.value) {
            showElement(saldoDisplay);
        } else {
            hideElement(saldoDisplay);
        }
        
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
            const stokHint = item.querySelector('.stok-hint');
            
            if (stok > 0) {
                stokHint.textContent = `✅ Stok tersedia: ${stok}`;
                stokHint.className = 'stok-hint available';
            } else {
                stokHint.textContent = '❌ Stok habis';
                stokHint.className = 'stok-hint unavailable';
            }
            
            const jumlahInput = item.querySelector('.jumlah-input');
            jumlahInput.max = stok;
            
            const stokWarning = item.querySelector('.stok-warning');
            if (stok <= 0) {
                jumlahInput.value = 0;
                jumlahInput.disabled = true;
                stokWarning.textContent = '🚫 Produk ini habis stok!';
                showElement(stokWarning);
            } else {
                jumlahInput.disabled = false;
                hideElement(stokWarning);
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
                alert(`⚠️ Stok tersedia hanya: ${stok}`);
            }
            
            updateSubtotal(item);
        }
    });
    
    function updateSubtotal(item) {
        const harga = parseFloat(item.querySelector('.harga-satuan').value) || 0;
        const jumlah = parseInt(item.querySelector('.jumlah-input').value) || 0;
        const subtotal = harga * jumlah;
        
        item.querySelector('.subtotal-value').value = subtotal;
        item.querySelector('.subtotal-display').value = 'Rp ' + formatRupiah(subtotal);
        
        calculateTotal();
    }
    
    function calculateTotal() {
        const subtotalInputs = document.querySelectorAll('.subtotal-value');
        let total = 0;
        
        subtotalInputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        
        document.getElementById('total_harga').value = total;
        document.getElementById('total_display').textContent = formatRupiah(total);
        
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
            kembalianDisplay.style.color = '#0ea5e9';
        } else {
            jumlahBayarInput.disabled = false;
            kembalianLabel.textContent = 'Kembalian';
            kembalianDisplay.style.color = '#059669';
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
        const saldoWarning = document.getElementById('saldo_warning');
        
        if (metodePembayaran === 'saldo' && jenisPelanggan === 'murid') {
            const saldoMurid = parseFloat(document.getElementById('saldo_murid').value) || 0;
            kembalian = saldoMurid >= total ? (saldoMurid - total) : 0;
            
            if (total > saldoMurid) {
                showElement(saldoWarning);
            } else {
                hideElement(saldoWarning);
            }
        } else {
            kembalian = jumlahBayar >= total ? (jumlahBayar - total) : 0;
            hideElement(saldoWarning);
        }
        
        document.getElementById('kembalian').value = kembalian;
        document.getElementById('kembalian_display').value = 'Rp ' + formatRupiah(kembalian);
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
        hideElement(newItem.querySelector('.stok-warning'));
        
        // Show remove button
        showElement(newItem.querySelector('.remove-item'));
        
        orderItems.appendChild(newItem);
        itemCount++;
        
        // Add animation
        newItem.style.animation = 'fadeIn 0.5s ease-out';
        
        updateRemoveButtons();
    });
    
    // Remove item functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            const item = e.target.closest('.order-item');
            item.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => {
                item.remove();
                updateRemoveButtons();
                calculateTotal();
            }, 300);
        }
    });
    
    function updateRemoveButtons() {
        const items = document.querySelectorAll('.order-item');
        items.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-item');
            if (items.length > 1) {
                showElement(removeBtn);
            } else {
                hideElement(removeBtn);
            }
        });
    }
    
    // Form submission with loading state
    document.getElementById('kasirForm').addEventListener('submit', function(e) {
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Validate form before submission
        if (!validateForm()) {
            e.preventDefault();
            return;
        }
        
        // Add loading state
        submitBtn.innerHTML = '⏳ Memproses...';
        submitBtn.disabled = true;
        form.classList.add('loading');
        
        // Re-enable form if there's an error (will be handled by server)
        setTimeout(() => {
            submitBtn.innerHTML = '✅ Buat Pesanan';
            submitBtn.disabled = false;
            form.classList.remove('loading');
        }, 5000);
    });
    
    function validateForm() {
        const jenisPelanggan = document.getElementById('jenis_pelanggan').value;
        const namaPelanggan = document.getElementById('nama_pelanggan').value;
        const metodePembayaran = document.getElementById('metode_pembayaran').value;
        const jumlahBayar = parseFloat(document.getElementById('jumlah_bayar').value) || 0;
        const total = parseFloat(document.getElementById('total_harga').value) || 0;
        
        // Check if customer is selected
        if (!jenisPelanggan) {
            alert('⚠️ Silakan pilih jenis pelanggan!');
            return false;
        }
        
        if (!namaPelanggan) {
            alert('⚠️ Silakan masukkan nama pelanggan!');
            return false;
        }
        
        // Check if there are items
        const items = document.querySelectorAll('.order-item');
        let hasValidItems = false;
        
        items.forEach(item => {
            const productSelect = item.querySelector('.product-select');
            const jumlahInput = item.querySelector('.jumlah-input');
            
            if (productSelect.value && parseInt(jumlahInput.value) > 0) {
                hasValidItems = true;
            }
        });
        
        if (!hasValidItems) {
            alert('⚠️ Silakan pilih minimal satu produk!');
            return false;
        }
        
        // Check payment amount
        if (metodePembayaran === 'tunai' && jumlahBayar < total) {
            alert('⚠️ Jumlah bayar tidak mencukupi!');
            return false;
        }
        
        // Check student balance
        if (metodePembayaran === 'saldo' && jenisPelanggan === 'murid') {
            const saldoMurid = parseFloat(document.getElementById('saldo_murid').value) || 0;
            if (saldoMurid < total) {
                alert('⚠️ Saldo murid tidak mencukupi!');
                return false;
            }
        }
        
        return true;
    }
    
    // Auto-calculate payment for cash
    document.getElementById('total_harga').addEventListener('change', function() {
        const metodePembayaran = document.getElementById('metode_pembayaran').value;
        if (metodePembayaran === 'tunai') {
            const total = parseFloat(this.value) || 0;
            document.getElementById('jumlah_bayar').value = total;
            updateKembalian();
        }
    });
    
    // Add fade out animation for remove
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }
    `;
    document.head.appendChild(style);
    
    // Initialize
    updateRemoveButtons();
    calculateTotal();
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + Enter to submit form
        if (e.ctrlKey && e.key === 'Enter') {
            document.getElementById('kasirForm').dispatchEvent(new Event('submit'));
        }
        
        // Ctrl + A to add new item
        if (e.ctrlKey && e.key === 'a') {
            e.preventDefault();
            document.getElementById('add_item').click();
        }
    });
    
    // Add tooltips for better UX
    const tooltips = {
        'jenis_pelanggan': 'Pilih jenis pelanggan: Murid (dapat menggunakan saldo), Guru, atau Staff',
        'nama_murid': 'Pilih nama murid dari daftar. Saldo akan ditampilkan otomatis',
        'metode_pembayaran': 'Pilih metode pembayaran. Saldo hanya tersedia untuk murid',
        'jumlah_bayar': 'Masukkan jumlah uang yang dibayarkan'
    };
    
    Object.keys(tooltips).forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.title = tooltips[id];
        }
    });
    
    // Add real-time search for products
    function addProductSearch() {
        const productSelects = document.querySelectorAll('.product-select');
        productSelects.forEach(select => {
            select.addEventListener('focus', function() {
                // Could add search functionality here
            });
        });
    }
    
    // Initialize product search
    addProductSearch();
    
    // Update product search when adding new items
    const originalAddItem = document.getElementById('add_item').onclick;
    document.getElementById('add_item').addEventListener('click', function() {
        setTimeout(() => {
            addProductSearch();
        }, 100);
    });
});
</script>
@endsection