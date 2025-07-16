
@extends('layouts.supplier')

@section('content')
<style>
/* Reset and Base Styles */
* {
    box-sizing: border-box;
}

/* Container and Layout */
.kasir-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.kasir-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

/* Header */
.kasir-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.kasir-title {
    font-size: 28px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.kasir-date {
    font-size: 14px;
    opacity: 0.9;
    background: rgba(255, 255, 255, 0.2);
    padding: 8px 16px;
    border-radius: 20px;
    backdrop-filter: blur(10px);
}

/* Main Content */
.kasir-content {
    padding: 32px;
}

/* Alert Styles */
.alert {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    border-left: 4px solid;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.alert-error {
    background: #fef2f2;
    border-left-color: #ef4444;
    color: #991b1b;
}

.alert-success {
    background: #f0fdf4;
    border-left-color: #22c55e;
    color: #166534;
}

.alert-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    margin-top: 2px;
}

.alert-content {
    flex: 1;
}

.alert-title {
    font-weight: 600;
    margin-bottom: 8px;
}

.alert-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.alert-list li {
    margin-bottom: 4px;
    padding-left: 16px;
    position: relative;
}

.alert-list li:before {
    content: "•";
    position: absolute;
    left: 0;
    color: currentColor;
}

/* Section Styles */
.kasir-section {
    margin-bottom: 32px;
    border-radius: 16px;
    border: 2px solid;
    overflow: hidden;
    background: white;
}

.section-customer {
    border-color: #3b82f6;
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
}

.section-orders {
    border-color: #10b981;
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
}

.section-summary {
    border-color: #f59e0b;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
}

.section-header {
    background: rgba(255, 255, 255, 0.8);
    padding: 20px 24px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
}

.section-title {
    font-size: 20px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-content {
    padding: 24px;
}

/* Form Elements */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-label .required {
    color: #ef4444;
    margin-left: 4px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #d1d5db;
    border-radius: 10px;
    font-size: 16px;
    transition: all 0.2s ease;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    transform: translateY(-1px);
}

.form-control:disabled {
    background: #f9fafb;
    color: #6b7280;
    cursor: not-allowed;
}

.form-control.readonly {
    background: #f9fafb;
    font-weight: 600;
}

/* Grid System */
.grid {
    display: grid;
    gap: 24px;
}

.grid-2 {
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
}

.grid-3 {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
}

.grid-12 {
    grid-template-columns: repeat(12, 1fr);
    gap: 16px;
}

.col-2 { grid-column: span 2; }
.col-3 { grid-column: span 3; }
.col-5 { grid-column: span 5; }
.col-12 { grid-column: span 12; }

@media (max-width: 768px) {
    .grid-12 > * {
        grid-column: span 12 !important;
    }
}

/* Customer Section */
.customer-section {
    display: none;
}

.saldo-display {
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.3);
    padding: 12px 16px;
    border-radius: 10px;
    color: #1e40af;
    font-weight: 600;
    margin-top: 12px;
    display: none;
}

/* Order Items */
.order-items {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.order-item {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    position: relative;
    transition: all 0.3s ease;
}

.order-item:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.order-item-header {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 100px;
    gap: 16px;
    margin-bottom: 16px;
    align-items: end;
}

@media (max-width: 768px) {
    .order-item-header {
        grid-template-columns: 1fr;
        gap: 12px;
    }
}

.stock-hint {
    font-size: 12px;
    margin-top: 4px;
    font-weight: 500;
}

.stock-hint.success {
    color: #059669;
}

.stock-hint.error {
    color: #dc2626;
}

.stock-warning {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    padding: 12px 16px;
    border-radius: 8px;
    margin-top: 12px;
    font-weight: 500;
    display: none;
}

/* Buttons */
.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    text-decoration: none;
    justify-content: center;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn:active {
    transform: translateY(0);
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
}

.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #047857 100%);
    color: white;
}

.btn-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.btn-add {
    background: linear-gradient(135deg, #10b981 0%, #047857 100%);
    color: white;
    margin-bottom: 20px;
}

.btn-submit {
    background: linear-gradient(135deg, #10b981 0%, #047857 100%);
    color: white;
    padding: 16px 32px;
    font-size: 18px;
    font-weight: 700;
}

.btn-icon {
    width: 16px;
    height: 16px;
}

.btn-remove {
    width: 100%;
    padding: 8px 12px;
    font-size: 12px;
}

/* Total Display */
.total-display {
    background: white;
    border: 3px solid #3b82f6;
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    margin-bottom: 24px;
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.1);
}

.total-label {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
    font-weight: 500;
}

.total-amount {
    font-size: 36px;
    font-weight: 800;
    color: #1e40af;
    margin: 0;
}

/* Balance Warning */
.balance-warning {
    background: #fef2f2;
    border: 2px solid #fecaca;
    color: #991b1b;
    padding: 16px 20px;
    border-radius: 12px;
    margin-top: 16px;
    font-weight: 600;
    display: none;
}

.balance-warning-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.balance-warning-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

/* Submit Section */
.submit-section {
    display: flex;
    justify-content: flex-end;
    padding-top: 32px;
    border-top: 1px solid #e5e7eb;
    margin-top: 32px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .kasir-container {
        padding: 12px;
    }
    
    .kasir-content {
        padding: 20px;
    }
    
    .kasir-header {
        padding: 20px;
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    
    .kasir-title {
        font-size: 24px;
    }
    
    .section-content {
        padding: 16px;
    }
    
    .total-amount {
        font-size: 28px;
    }
    
    .btn-submit {
        width: 100%;
    }
}

/* Loading State */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in {
    animation: fadeIn 0.3s ease-out;
}

/* Form Validation */
.form-control.error {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.form-control.success {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

/* Hover Effects */
.hover-lift:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<div class="kasir-container">
    <div class="kasir-card">
        <div class="kasir-header">
            <h1 class="kasir-title">
                <span>💳</span>
                Kasir - Buat Pesanan
            </h1>
            <div class="kasir-date">
                {{ date('d M Y, H:i') }}
            </div>
        </div>
        
        <div class="kasir-content">
            @if ($errors->any())
                <div class="alert alert-error">
                    <svg class="alert-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="alert-content">
                        <div class="alert-title">Terjadi kesalahan:</div>
                        <ul class="alert-list">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            
            @if (session('success'))
                <div class="alert alert-success">
                    <svg class="alert-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div class="alert-content">
                        <div class="alert-title">{{ session('success') }}</div>
                    </div>
                </div>
            @endif
            
            <form action="{{ route('kasir.store') }}" method="POST" id="kasirForm">
                @csrf
                
                <!-- Customer Information Section -->
                <div class="kasir-section section-customer">
                    <div class="section-header">
                        <h2 class="section-title">
                            <span>👤</span>
                            Informasi Pelanggan
                        </h2>
                    </div>
                    <div class="section-content">
                        <div class="grid grid-2">
                            <div class="form-group">
                                <label class="form-label">
                                    Jenis Pelanggan
                                    <span class="required">*</span>
                                </label>
                                <select name="jenis_pelanggan" id="jenis_pelanggan" class="form-control" required>
                                    <option value="">Pilih jenis pelanggan...</option>
                                    <option value="murid">🎓 Murid</option>
                                    <option value="guru">👨‍🏫 Guru</option>
                                    <option value="staff">👥 Staff</option>
                                </select>
                            </div>
                            
                            <!-- Student Section -->
                            <div id="murid_section" class="customer-section">
                                <div class="form-group">
                                    <label class="form-label">
                                        Nama Murid
                                        <span class="required">*</span>
                                    </label>
                                    <select name="nama_pelanggan_murid" id="nama_murid" class="form-control">
                                        <option value="">Pilih murid...</option>
                                        @foreach($murids as $murid)
                                            <option value="{{ $murid->name }}" data-saldo="{{ $murid->saldo }}">
                                                {{ $murid->name }} - Kelas {{ $murid->kelas }}
                                                (Saldo: Rp {{ number_format($murid->saldo, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="saldo_display" class="saldo-display">
                                        <span>💳</span>
                                        <span id="saldo_text"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Teacher Section -->
                            <div id="guru_section" class="customer-section">
                                <div class="form-group">
                                    <label class="form-label">
                                        Nama Guru
                                        <span class="required">*</span>
                                    </label>
                                    <input type="text" name="nama_pelanggan_guru" id="nama_guru" class="form-control" placeholder="Masukkan nama guru...">
                                </div>
                            </div>
                            
                            <!-- Staff Section -->
                            <div id="staff_section" class="customer-section">
                                <div class="form-group">
                                    <label class="form-label">
                                        Nama Staff
                                        <span class="required">*</span>
                                    </label>
                                    <input type="text" name="nama_pelanggan_staff" id="nama_staff" class="form-control" placeholder="Masukkan nama staff...">
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="nama_pelanggan" id="nama_pelanggan">
                        <input type="hidden" name="saldo_murid" id="saldo_murid" value="0">
                    </div>
                </div>
                
                <!-- Order Items Section -->
                <div class="kasir-section section-orders">
                    <div class="section-header">
                        <h2 class="section-title">
                            <span>🛒</span>
                            Daftar Pesanan
                        </h2>
                    </div>
                    <div class="section-content">
                        <button type="button" id="add_item" class="btn btn-add">
                            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah Item
                        </button>
                        
                        <div id="order_items" class="order-items">
                            <div class="order-item fade-in">
                                <div class="order-item-header">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Produk
                                            <span class="required">*</span>
                                        </label>
                                        <select name="orderItems[0][product_id]" class="product-select form-control" required>
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
                                        <div class="stock-hint"></div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">
                                            Qty
                                            <span class="required">*</span>
                                        </label>
                                        <input type="number" name="orderItems[0][jumlah]" class="jumlah-input form-control" min="1" value="1" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Subtotal</label>
                                        <input type="text" class="subtotal-display form-control readonly" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="remove-item btn btn-danger btn-remove" style="display: none;">
                                            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Catatan Item</label>
                                    <textarea name="orderItems[0][catatan_item]" class="form-control" rows="2" placeholder="Tambahan, kurang pedas, dll..."></textarea>
                                </div>
                                
                                <div class="stock-warning">
                                    <span>⚠️</span>
                                    <span class="warning-text"></span>
                                </div>
                                
                                <input type="hidden" name="orderItems[0][harga_satuan]" class="harga-satuan">
                                <input type="hidden" name="orderItems[0][subtotal]" class="subtotal-value">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary Section -->
                <div class="kasir-section section-summary">
                    <div class="section-header">
                        <h2 class="section-title">
                            <span>💳</span>
                            Ringkasan Pesanan
                        </h2>
                    </div>
                    <div class="section-content">
                        <div class="total-display">
                            <div class="total-label">TOTAL HARGA</div>
                            <div class="total-amount">
                                Rp <span id="total_display">0</span>
                            </div>
                        </div>
                        
                        <div class="grid grid-3">
                            <div class="form-group">
                                <label class="form-label">
                                    Metode Pembayaran
                                    <span class="required">*</span>
                                </label>
                                <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                                    <option value="tunai">💵 Tunai</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    Jumlah Bayar
                                    <span class="required">*</span>
                                </label>
                                <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-control" min="0" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" id="kembalian_label">Kembalian</label>
                                <input type="text" id="kembalian_display" class="form-control readonly" readonly>
                            </div>
                        </div>
                        
                        <div id="saldo_warning" class="balance-warning">
                            <div class="balance-warning-content">
                                <svg class="balance-warning-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span>Saldo tidak mencukupi untuk pesanan ini!</span>
                            </div>
                        </div>
                        
                        <input type="hidden" name="total_harga" id="total_harga">
                        <input type="hidden" name="kembalian" id="kembalian">
                    </div>
                </div>
                
                <div class="submit-section">
                    <button type="submit" class="btn btn-submit">
                        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Buat Pesanan
                    </button>
                </div>
            </form>
        </div>
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