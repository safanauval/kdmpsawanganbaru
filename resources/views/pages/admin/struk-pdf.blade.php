<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Struk Pembayaran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: 58mm 80mm;
            margin: 3mm;
            padding: 3mm;
        }

        body {
            font-family: 'Courier New', 'Consolas', monospace;
            font-size: 8px;
            line-height: 1.2;
            width: 56mm;
            margin: 2mm;
            padding: 2mm;
            background: #fff;
            color: #000;
        }

        /* Text Utilities */
        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-xs {
            font-size: 7px;
        }

        .text-sm {
            font-size: 8px;
        }

        .text-lg {
            font-size: 12px;
        }

        .text-gray-500 {
            color: #6b7280;
        }

        .text-green-600 {
            color: #16a34a;
        }

        .font-bold {
            font-weight: bold;
        }

        .font-mono {
            font-family: 'Courier New', monospace;
        }

        /* Spacing */
        .p-4 {
            padding: 4px;
        }

        .py-1 {
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .mb-2 {
            margin-bottom: 4px;
        }

        .mt-4 {
            margin-top: 8px;
        }

        .pb-2 {
            padding-bottom: 4px;
        }

        /* Borders */
        .border-b {
            border-bottom: 1px dashed #000;
        }

        .border-t {
            border-top: 1px dashed #000;
        }

        .border-b-solid {
            border-bottom: 1px solid #000;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        th,
        td {
            padding: 2px 2px;
        }

        thead th {
            border-bottom: 1px dashed #000;
            font-weight: bold;
            font-size: 8px;
        }

        tfoot td {
            padding: 2px 2px;
        }

        /* Header Toko */
        .header-title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .header-text {
            font-size: 8px;
            text-align: center;
            line-height: 1.3;
        }

        .header-divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 4px 0;
        }

        /* Info Order */
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 1px 0;
            font-size: 8px;
        }

        /* Footer */
        .footer-text {
            text-align: center;
            font-size: 8px;
            color: #6b7280;
            margin-top: 8px;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
                width: 58mm;
            }

            .no-print {
                display: none !important;
            }
        }

        @media screen {
            body {
                border: 1px solid #ddd;
                margin: 10px auto;
                padding: 5px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
        }
    </style>
</head>

<body>
    <div class="p-4 font-mono">
        {{-- Header Toko --}}
        <div class="text-center border-b pb-2 mb-2">
            <h2 class="font-bold text-lg">
                {{ \App\Helpers\SettingHelper::get('company_name', 'Koperasi Merah Putih') }}
            </h2>
            <p class="header-text">{{ \App\Helpers\SettingHelper::get('address', 'Alamat belum diatur') }}</p>
            <p class="header-text">Telp: {{ \App\Helpers\SettingHelper::get('phone', 'Telepon belum diatur') }}</p>
            <p class="header-text">{{ now()->format('d/m/Y H:i:s') }}</p>
        </div>

        {{-- Informasi Order --}}
        <div class="mb-2">
            <p class="info-row"><strong>No. Order:</strong> {{ $order->order_id }}</p>
            <p class="info-row"><strong>Kasir:</strong> {{ auth()->user()->name ?? 'Admin' }}</p>
            <p class="info-row"><strong>Pelanggan:</strong>
                {{ $order->nama_pelanggan ?? $order->customer_name ?: 'Umum' }}</p>
            <p class="info-row"><strong>Metode:</strong> {{ ucfirst($order->payment_method) }}</p>
        </div>

        {{-- Tabel Item --}}
        <table class="border-t border-b my-2">
            <thead>
                <tr>
                    <th class="text-left py-1">Item</th>
                    <th class="text-right py-1">Qty</th>
                    <th class="text-right py-1">Harga</th>
                    <th class="text-right py-1">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->cart_items as $item)
                    <tr>
                        <td class="py-1">{{ $item['name'] }}</td>
                        <td class="text-right py-1">{{ $item['quantity'] }}</td>
                        <td class="text-right py-1">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                        <td class="text-right py-1">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t">
                {{-- Subtotal --}}
                @php
                    $subtotal = collect($order->cart_items)->sum(fn($item) => $item['price'] * $item['quantity']);
                @endphp
                <tr>
                    <td colspan="3" class="text-right py-1">Subtotal:</td>
                    <td class="text-right py-1">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>

                {{-- Diskon --}}
                @if($order->discount_amount > 0)
                    <tr class="text-green-600">
                        <td colspan="3" class="text-right py-1">Diskon:</td>
                        <td class="text-right py-1">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif

                {{-- Total --}}
                <tr>
                    <th colspan="3" class="text-right py-1">Total:</th>
                    <th class="text-right py-1">Rp {{ number_format($order->total, 0, ',', '.') }}</th>
                </tr>

                {{-- Pembayaran Tunai --}}
                @if($order->payment_method === 'tunai' && $order->payment_amount)
                    <tr>
                        <td colspan="3" class="text-right py-1">Bayar:</td>
                        <td class="text-right py-1">Rp {{ number_format($order->payment_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right py-1">Kembali:</td>
                        <td class="text-right py-1">Rp
                            {{ number_format($order->payment_amount - $order->total, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
            </tfoot>
        </table>

        {{-- Footer --}}
        <div class="footer-text mt-4">
            {{ \App\Helpers\SettingHelper::get('footer_text', 'Terima kasih atas kunjungan Anda') }}<br>
            *** Simpan struk sebagai bukti ***
        </div>
    </div>
</body>

</html>