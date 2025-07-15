@extends('layouts.supplier')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sistem Kasir</h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="resetBtn">
            <i class="fas fa-refresh fa-sm text-white-50"></i> Reset Form
        </button>
    </div>

    <div class="row">
        <!-- Form Kasir -->
        <div class="col-xl-8 col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Transaksi</h6>
                </div>
                <div class="card-body">
                    <form id="kasirForm">
                        @csrf

                        <!-- Informasi Pelanggan -->
                        <div class="mb-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="m-0"><i class="fas fa-user mr-2"></i>Informasi Pelanggan</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="jenis_pelanggan">Jenis Pelanggan</label>
                                                <select class="form-control" id="jenis_pelanggan" name="jenis_pelanggan" required>
                                                    <option value="">Pilih jenis pelanggan...</option>
                                                    <option value="murid">🎓 Murid</option>
                                                    <option value="guru">👨‍🏫 Guru</option>
                                                    <option value="staff">👥 Staff</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nama Pelanggan</label>
                                                <!-- Container untuk input nama pelanggan -->
                                                <div id="nama_pelanggan_container">
                                                    <!-- Select untuk murid -->
                                                    <select class="form-control" id="nama_pelanggan_select" name="nama_pelanggan_select" style="display: none;">
                                                        <option value="">Pilih nama murid...</option>
                                                    </select>

                                                    <!-- Input manual untuk guru/staff -->
                                                    <input type="text" class="form-control" id="nama_pelanggan_input" name="nama_pelanggan_input" placeholder="Masukkan nama pelanggan..." style="display: none;">

                                                    <!-- Default placeholder -->
                                                    <input type="text" class="form-control" id="nama_pelanggan_placeholder" placeholder="Silakan pilih jenis pelanggan dulu..." disabled readonly>
                                                </div>

                                                <!-- Hidden input untuk menampung nilai nama pelanggan yang sebenarnya -->
                                                <input type="hidden" id="nama_pelanggan" name="nama_pelanggan">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Saldo Murid (hanya tampil untuk murid) -->
                                    <div id="saldo_section" class="row" style="display: none;">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Saldo Murid Saat Ini</label>
                                                <div class="alert alert-info">
                                                    <strong id="saldo_display">Rp 0</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Kelas</label>
                                                <div class="alert alert-secondary">
                                                    <strong id="kelas_display">-</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Info pembayaran untuk guru/staff -->
                                    <div id="payment_info" class="row" style="display: none;">
                                        <div class="col-md-12">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                <strong>Informasi:</strong> Guru dan Staff hanya dapat melakukan pembayaran tunai.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Pesanan -->
                        <div class="mb-4">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="m-0"><i class="fas fa-shopping-cart mr-2"></i>Daftar Pesanan</h6>
                                </div>
                                <div class="card-body">
                                    <div id="orderItems">
                                        <!-- Item pesanan akan ditambahkan di sini -->
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm" id="addItemBtn">
                                        <i class="fas fa-plus"></i> Tambah Item
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Pesanan -->
                        <div class="mb-4">
                            <div class="card">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="m-0"><i class="fas fa-credit-card mr-2"></i>Ringkasan Pesanan</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="h4">TOTAL HARGA</label>
                                                <div class="alert alert-primary">
                                                    <h3 id="total_display">Rp 0</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="metode_pembayaran">Metode Pembayaran</label>
                                                <select class="form-control" id="metode_pembayaran" name="metode_pembayaran" required>
                                                    <option value="">Pilih metode pembayaran...</option>
                                                    <option value="tunai">💵 Tunai</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="jumlah_bayar">Jumlah Bayar</label>
                                                <input type="number" class="form-control" id="jumlah_bayar" name="jumlah_bayar" min="0" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label id="kembalian_label">KEMBALIAN</label>
                                                <div class="alert alert-success">
                                                    <strong id="kembalian_display">Rp 0</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Warning saldo tidak cukup -->
                                    <div id="saldo_warning" class="alert alert-danger" style="display: none;">
                                        <strong>⚠️ Saldo tidak mencukupi untuk pesanan ini!</strong>
                                    </div>

                                    <!-- Catatan pesanan -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="catatan">Catatan Pesanan</label>
                                                <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Catatan tambahan untuk pesanan ini..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-save"></i> Proses Transaksi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-xl-4 col-lg-4">
            <!-- Produk Populer -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Produk Populer</h6>
                </div>
                <div class="card-body">
                    <div id="produk_populer">
                        <!-- Akan diisi dengan AJAX -->
                    </div>
                </div>
            </div>

            <!-- Transaksi Terakhir -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Transaksi Terakhir</h6>
                </div>
                <div class="card-body">
                    <div id="transaksi_terakhir">
                        <!-- Akan diisi dengan AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden fields -->
<input type="hidden" id="total_harga" value="0">
<input type="hidden" id="saldo_murid" value="0">
<input type="hidden" id="kembalian" value="0">
<input type="hidden" id="itemCounter" value="0">

@endsection

@section('scripts')
<script>
let itemCounter = 0;
let products = {};
let customers = {};

// DEFINISI FUNGSI-FUNGSI UTAMA DULU
// Add order item
function addOrderItem() {
    itemCounter++;
    const itemHtml = `
        <div class="order-item mb-3 p-3 border rounded" id="item_${itemCounter}">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="product_${itemCounter}">Produk</label>
                        <select class="form-control product-select" id="product_${itemCounter}" name="items[${itemCounter}][product_id]" required>
                            <option value="">Pilih produk...</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="quantity_${itemCounter}">Qty</label>
                        <input type="number" class="form-control quantity-input" id="quantity_${itemCounter}" name="items[${itemCounter}][jumlah]" min="1" value="1" required>
                        <small class="text-muted stock-info"></small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="subtotal_${itemCounter}">Subtotal</label>
                        <input type="text" class="form-control subtotal-display" id="subtotal_${itemCounter}" readonly>
                        <input type="hidden" class="subtotal-value" name="items[${itemCounter}][subtotal]" value="0">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm d-block remove-item-btn" data-item-id="${itemCounter}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="catatan_item_${itemCounter}">Catatan Item</label>
                        <textarea class="form-control" id="catatan_item_${itemCounter}" name="items[${itemCounter}][catatan_item]" rows="2" placeholder="Tambahan, kurang pedas, dll..."></textarea>
                    </div>
                </div>
            </div>
            <div class="stock-warning"></div>
        </div>
    `;

    $('#orderItems').append(itemHtml);

    // Populate product options
    const productSelect = $(`#product_${itemCounter}`);
    Object.values(products).forEach(function(product) {
        const stokInfo = product.stok > 0 ? ` (Stok: ${product.stok})` : ' (HABIS)';
        productSelect.append(`<option value="${product.id}" data-harga="${product.harga}" data-stok="${product.stok}">${product.nama_produk} - ${formatRupiah(product.harga)}${stokInfo}</option>`);
    });

    // Bind events
    bindItemEvents(itemCounter);
}

// Remove order item
function removeOrderItem(itemId) {
    $(`#item_${itemId}`).remove();
    calculateTotal();
}

// Format rupiah
function formatRupiah(angka) {
    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Reset form
function resetForm() {
    $('#kasirForm')[0].reset();
    $('#orderItems').empty();
    $('#saldo_section').hide();
    $('#payment_info').hide();
    $('#saldo_warning').hide();

    // Reset nama pelanggan fields
    $('#nama_pelanggan_select').hide();
    $('#nama_pelanggan_input').hide();
    $('#nama_pelanggan_placeholder').show();
    $('#nama_pelanggan').val('');

    itemCounter = 0;
    updatePaymentMethods();
    addOrderItem();
    calculateTotal();
}

// Update payment methods
function updatePaymentMethods() {
    const metodePembayaran = $('#metode_pembayaran');
    const jenisPelanggan = $('#jenis_pelanggan').val();

    metodePembayaran.empty();
    metodePembayaran.append('<option value="">Pilih metode pembayaran...</option>');
    metodePembayaran.append('<option value="tunai">💵 Tunai</option>');

    if (jenisPelanggan === 'murid') {
        metodePembayaran.append('<option value="saldo">💳 Saldo Murid</option>');
    }
}

// Calculate total
function calculateTotal() {
    let total = 0;

    $('.subtotal-value').each(function() {
        total += parseInt($(this).val()) || 0;
    });

    $('#total_harga').val(total);
    $('#total_display').text(formatRupiah(total));

    // Calculate kembalian
    const metodePembayaran = $('#metode_pembayaran').val();
    const jumlahBayar = parseInt($('#jumlah_bayar').val()) || 0;
    const saldoMurid = parseInt($('#saldo_murid').val()) || 0;

    let kembalian = 0;
    let showSaldoWarning = false;

    if (metodePembayaran === 'saldo' && $('#jenis_pelanggan').val() === 'murid') {
        $('#jumlah_bayar').val(total);
        kembalian = saldoMurid >= total ? (saldoMurid - total) : 0;
        showSaldoWarning = total > saldoMurid;
    } else {
        kembalian = jumlahBayar >= total ? (jumlahBayar - total) : 0;
    }

    $('#kembalian').val(kembalian);
    $('#kembalian_display').text(formatRupiah(kembalian));

    // Show/hide saldo warning
    if (showSaldoWarning) {
        $('#saldo_warning').show();
    } else {
        $('#saldo_warning').hide();
    }
}

// Update subtotal
function updateSubtotal(itemId) {
    const itemContainer = $(`#item_${itemId}`);
    const selectedOption = itemContainer.find('.product-select option:selected');
    const harga = selectedOption.data('harga') || 0;
    const quantity = parseInt(itemContainer.find('.quantity-input').val()) || 0;
    const subtotal = harga * quantity;

    itemContainer.find('.subtotal-display').val(formatRupiah(subtotal));
    itemContainer.find('.subtotal-value').val(subtotal);

    calculateTotal();
}

// Bind events untuk item
function bindItemEvents(itemId) {
    const itemContainer = $(`#item_${itemId}`);

    // Product change
    itemContainer.find('.product-select').change(function() {
        const selectedOption = $(this).find('option:selected');
        const harga = selectedOption.data('harga') || 0;
        const stok = selectedOption.data('stok') || 0;
        const quantity = itemContainer.find('.quantity-input');
        const stockInfo = itemContainer.find('.stock-info');
        const stockWarning = itemContainer.find('.stock-warning');

        // Update stock info
        if (stok > 0) {
            stockInfo.text(`Stok tersedia: ${stok}`);
            stockInfo.removeClass('text-danger').addClass('text-success');
        } else {
            stockInfo.text('Stok habis');
            stockInfo.removeClass('text-success').addClass('text-danger');
        }

        // Update quantity max
        quantity.attr('max', stok);

        if (stok <= 0) {
            quantity.val(0);
            stockWarning.html('<div class="alert alert-danger">🚫 Produk ini habis stok!</div>');
        } else if (stok <= 5) {
            stockWarning.html(`<div class="alert alert-warning">⚠️ Stok tinggal ${stok} item!</div>`);
        } else {
            stockWarning.html('');
        }

        updateSubtotal(itemId);
    });

    // Quantity change
    itemContainer.find('.quantity-input').on('input', function() {
        const productSelect = itemContainer.find('.product-select');
        const selectedOption = productSelect.find('option:selected');
        const stok = selectedOption.data('stok') || 0;
        const quantity = parseInt($(this).val()) || 0;

        if (quantity > stok) {
            $(this).val(stok);
            alert(`Stok tersedia hanya: ${stok}`);
        }

        updateSubtotal(itemId);
    });
}

// Load products
function loadProducts() {
    $.ajax({
        url: '{{ route("kasir.products") }}',
        method: 'GET',
        success: function(response) {
            products = response.products;
        },
        error: function(xhr) {
            console.error('Error loading products:', xhr);
        }
    });
}

// Load customers
function loadCustomers() {
    $.ajax({
        url: '{{ route("kasir.customers") }}',
        method: 'GET',
        success: function(response) {
            customers = response.customers;
        },
        error: function(xhr) {
            console.error('Error loading customers:', xhr);
        }
    });
}

// Load produk populer
function loadProdukPopuler() {
    $.ajax({
        url: '{{ route("kasir.produk-populer") }}',
        method: 'GET',
        success: function(response) {
            let html = '';
            response.products.forEach(function(product) {
                html += `
                    <div class="d-flex align-items-center mb-2">
                        <div class="mr-3">
                            <div class="icon-circle bg-primary">
                                <i class="fas fa-box text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">${product.nama_produk}</div>
                            <div class="font-weight-bold">${formatRupiah(product.harga)}</div>
                        </div>
                    </div>
                `;
            });
            $('#produk_populer').html(html);
        },
        error: function(xhr) {
            console.error('Error loading popular products:', xhr);
        }
    });
}

// Load transaksi terakhir
function loadTransaksiTerakhir() {
    $.ajax({
        url: '{{ route("kasir.transaksi-terakhir") }}',
        method: 'GET',
        success: function(response) {
            let html = '';
            response.transactions.forEach(function(transaction) {
                html += `
                    <div class="d-flex align-items-center mb-2">
                        <div class="mr-3">
                            <div class="icon-circle bg-success">
                                <i class="fas fa-receipt text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">${transaction.nama_pelanggan}</div>
                            <div class="font-weight-bold">${formatRupiah(transaction.total_harga)}</div>
                        </div>
                    </div>
                `;
            });
            $('#transaksi_terakhir').html(html);
        },
        error: function(xhr) {
            console.error('Error loading recent transactions:', xhr);
        }
    });
}

// DOCUMENT READY DAN EVENT HANDLERS
$(document).ready(function() {
    // Load data saat halaman dimuat
    loadProducts();
    loadCustomers();
    loadProdukPopuler();
    loadTransaksiTerakhir();

    // Tambah item pertama
    addOrderItem();

    // Event handlers
    $('#addItemBtn').click(function() {
        addOrderItem();
    });

    $('#resetBtn').click(function() {
        resetForm();
    });

    // Handle remove item with event delegation
    $(document).on('click', '.remove-item-btn', function() {
        const itemId = $(this).data('item-id');
        removeOrderItem(itemId);
    });

    // Handle jenis pelanggan change
    $('#jenis_pelanggan').change(function() {
        const jenis = $(this).val();

        // Hide all input fields
        $('#nama_pelanggan_select').hide();
        $('#nama_pelanggan_input').hide();
        $('#nama_pelanggan_placeholder').hide();
        $('#saldo_section').hide();
        $('#payment_info').hide();

        // Reset values
        $('#nama_pelanggan_select').val('');
        $('#nama_pelanggan_input').val('');
        $('#nama_pelanggan').val('');
        $('#saldo_murid').val(0);
        $('#saldo_display').text('Rp 0');
        $('#kelas_display').text('-');

        if (jenis === 'murid') {
            // Show select for murid
            $('#nama_pelanggan_select').show();

            // Populate murid options
            const muridSelect = $('#nama_pelanggan_select');
            muridSelect.empty().append('<option value="">Pilih nama murid...</option>');

            if (customers.murid) {
                customers.murid.forEach(function(murid) {
                    muridSelect.append(`<option value="${murid.name}" data-saldo="${murid.saldo}" data-kelas="${murid.kelas}">${murid.display_name}</option>`);
                });
            }
        } else if (jenis === 'guru' || jenis === 'staff') {
            // Show input for guru/staff
            $('#nama_pelanggan_input').show();
            $('#payment_info').show();
        } else {
            // Show placeholder
            $('#nama_pelanggan_placeholder').show();
        }

        // Update payment methods
        updatePaymentMethods();
        calculateTotal();
    });

    // Handle nama pelanggan change (untuk murid)
    $('#nama_pelanggan_select').change(function() {
        const selectedOption = $(this).find('option:selected');
        const nama = $(this).val();
        const saldo = selectedOption.data('saldo') || 0;
        const kelas = selectedOption.data('kelas') || '-';

        // Update hidden field
        $('#nama_pelanggan').val(nama);

        $('#saldo_murid').val(saldo);
        $('#saldo_display').text(formatRupiah(saldo));
        $('#kelas_display').text(kelas);
        $('#saldo_section').show();

        calculateTotal();
    });

    // Handle nama pelanggan input (untuk guru/staff)
    $('#nama_pelanggan_input').on('input', function() {
        const nama = $(this).val();

        // Update hidden field
        $('#nama_pelanggan').val(nama);

        calculateTotal();
    });

    // Handle metode pembayaran change
    $('#metode_pembayaran').change(function() {
        const metode = $(this).val();
        const jumlahBayar = $('#jumlah_bayar');
        const kembalianLabel = $('#kembalian_label');

        if (metode === 'saldo' && $('#jenis_pelanggan').val() === 'murid') {
            jumlahBayar.prop('disabled', true);
            jumlahBayar.val($('#total_harga').val());
            kembalianLabel.text('SISA SALDO');
        } else {
            jumlahBayar.prop('disabled', false);
            kembalianLabel.text('KEMBALIAN');
        }

        calculateTotal();
    });

    // Handle jumlah bayar change
    $('#jumlah_bayar').on('input', function() {
        calculateTotal();
    });

    // Form submission
    $('#kasirForm').submit(function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: '{{ route("kasir.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Transaksi berhasil disimpan!');
                    resetForm();
                    loadTransaksiTerakhir();
                } else {
                    alert('Gagal menyimpan transaksi: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Error submitting form:', xhr);
                alert('Terjadi kesalahan: ' + xhr.responseText);
            }
        });
    });
});
</script>
@endsection