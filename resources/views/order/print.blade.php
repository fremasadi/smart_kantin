<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Order #{{ $order->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            background: white;
            color: black;
        }

        .receipt {
            width: 300px;
            margin: 0 auto;
            padding: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 10px;
            margin-bottom: 2px;
        }

        .order-info {
            margin-bottom: 15px;
        }

        .order-info table {
            width: 100%;
            font-size: 11px;
        }

        .order-info td {
            padding: 2px 0;
        }

        .items-header {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            font-weight: bold;
            text-align: center;
        }

        .items-table {
            width: 100%;
            margin: 10px 0;
        }

        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .item-name {
            font-weight: bold;
        }

        .item-notes {
            font-size: 10px;
            font-style: italic;
            color: #666;
            margin-left: 10px;
        }

        .item-qty {
            text-align: center;
            width: 30px;
        }

        .item-price {
            text-align: right;
            width: 60px;
        }

        .total-section {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }

        .total-final {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        .payment-info {
            margin: 15px 0;
            padding: 10px 0;
            border-top: 1px dashed #000;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #000;
            font-size: 10px;
        }

        .divider {
            text-align: center;
            margin: 10px 0;
            font-size: 10px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .receipt {
                width: 100%;
                max-width: 300px;
            }
            
            .no-print {
                display: none !important;
            }
            
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }

        /* Button untuk print manual jika diperlukan */
        .print-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px auto;
            display: block;
            font-size: 14px;
        }

        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    {{-- <!-- Tombol print manual (akan hilang saat print) -->
    <div class="no-print" style="text-align: center; padding: 20px;">
        <button class="print-button" onclick="window.print()">🖨️ Print Struk</button>
        <button class="print-button" onclick="window.close()" style="background: #6c757d;">❌ Tutup</button>
    </div> --}}

    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <div class="company-name">SD Sunan Ampel</div>
        </div>

        <!-- Order Info -->
        <div class="order-info">
            <table>
                <tr>
                    <td>No. Order</td>
                    <td>: #{{ $order->id }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: {{ $order->tanggal_pesanan->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Pelanggan</td>
                    <td>: {{ $order->nama_pelanggan }}</td>
                </tr>
            </table>
        </div>

        <!-- Items Header -->
        <div class="items-header">
            DETAIL PESANAN
        </div>

        <!-- Items List -->
        <table class="items-table">
            @foreach($items as $item)
            <tr>
                <td colspan="3" class="item-name">{{ $item->product->nama_produk }}</td>
            </tr>
            <tr>
                <td class="item-qty">{{ $item->jumlah }}x</td>
                <td class="item-price">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                <td class="item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($item->catatan_item)
            <tr>
                <td colspan="3" class="item-notes">* {{ $item->catatan_item }}</td>
            </tr>
            @endif
            <tr><td colspan="3" style="height: 5px;"></td></tr>
            @endforeach
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <span>Subtotal ({{ $total_items }} item)</span>
                <span>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
            </div>
            
            <div class="total-row total-final">
                <span>TOTAL</span>
                <span>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="payment-info">
            <div class="total-row">
                <span>Metode Bayar</span>
                <span>
                    @switch($order->metode_pembayaran)
                        @case('tunai')
                            💵 Tunai
                            @break
                        @case('transfer')
                            🏧 Transfer
                            @break
                        @case('qris')
                            📱 QRIS
                            @break
                        @default
                            {{ ucfirst($order->metode_pembayaran) }}
                    @endswitch
                </span>
            </div>
            <div class="total-row">
                <span>Jumlah Bayar</span>
                <span>Rp {{ number_format($order->jumlah_bayar, 0, ',', '.') }}</span>
            </div>
            @if($order->kembalian > 0)
            <div class="total-row">
                <span>Kembalian</span>
                <span>Rp {{ number_format($order->kembalian, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>

        <!-- Divider -->
        <div class="divider">=============================</div>

        <!-- Footer -->
        <div class="footer">
            <div>Terima kasih atas kunjungan Anda!</div>
            <div>Selamat menikmati makanan 😊</div>
            <div style="margin-top: 10px;">
                <small>Dicetak: {{ $print_date }}</small>
            </div>
        </div>
    </div>

    <script>
        // Auto print saat halaman dimuat
        window.onload = function() {
            // Delay sebentar untuk memastikan styling sudah load
            setTimeout(function() {
                window.print();
            }, 500);
        }

        // Tutup window setelah print (opsional)
        window.onafterprint = function() {
            // window.close(); // Uncomment jika ingin auto close setelah print
        }
    </script>
</body>
</html>