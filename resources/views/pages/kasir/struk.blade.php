<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $penjualan->no_transaksi }} - Apotek Tabah Farma</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo Apotek Tabah Farma.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #f3f4f6;
            color: #000;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* Top Action Bar for screen view */
        .action-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding: 10px 18px;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background-color: #465fff;
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #364bcc;
        }

        .btn-secondary {
            background-color: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background-color: #e5e7eb;
        }

        /* Receipt Paper Container */
        .receipt {
            width: 100%;
            max-width: 340px;
            background: #ffffff;
            padding: 24px 20px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            font-size: 13px;
            line-height: 1.4;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .header-title {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }

        .header-sub {
            font-size: 12px;
            color: #222;
        }

        .divider {
            border: none;
            border-top: 1px dashed #000000;
            margin: 10px 0;
        }

        .info-row {
            margin-bottom: 2px;
            font-size: 12px;
        }

        .table-header {
            display: flex;
            font-weight: bold;
            font-size: 12px;
            padding-bottom: 4px;
        }

        .item-row {
            display: flex;
            align-items: baseline;
            margin-bottom: 4px;
            font-size: 12px;
        }

        .col-barang {
            flex: 5;
            padding-right: 4px;
            word-break: break-word;
        }

        .col-qty {
            flex: 2;
            text-align: center;
        }

        .col-harga {
            flex: 2.5;
            text-align: right;
        }

        .col-sub {
            flex: 2.5;
            text-align: right;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 12px;
        }

        .total-row.grand-total {
            font-size: 14px;
            font-weight: 900;
        }

        .footer-note {
            font-size: 11px;
            text-align: center;
            margin-top: 8px;
            line-height: 1.3;
            color: #222;
        }

        /* ── Thermal Printer Optimization ── */
        @media print {
            @page {
                margin: 0;
                size: auto;
            }

            body {
                background: none !important;
                padding: 0 !important;
                margin: 0 !important;
                min-height: auto !important;
                display: block !important;
            }

            .action-bar {
                display: none !important;
            }

            .receipt {
                width: 100% !important;
                max-width: 100% !important;
                border: none !important;
                box-shadow: none !important;
                padding: 10px !important;
                margin: 0 !important;
            }
        }
    </style>
</head>

<body>

    {{-- Screen Only Action Bar --}}
    <div class="action-bar">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="ti ti-printer"></i> Cetak Struk
        </button>
    </div>

    {{-- Thermal Receipt Paper --}}
    <div class="receipt">
        {{-- Header Toko --}}
        <div class="text-center">
            <div class="header-title uppercase">APOTEK TABAH FARMA</div>
            <div class="header-sub">Jl. H. Ilyas No.72 Blangpidie</div>
        </div>

        <div class="divider"></div>

        {{-- Nomor & Tanggal Transaksi --}}
        <div class="font-bold info-row">{{ $penjualan->no_transaksi }}</div>
        <div class="info-row">{{ $penjualan->tanggal_transaksi->format('d/m/Y H:i') }}</div>

        <div class="divider"></div>

        {{-- Table Item Header --}}
        <div class="table-header">
            <div class="col-barang">Barang</div>
            <div class="col-qty">Qty</div>
            <div class="col-harga">Harga</div>
            <div class="col-sub">Sub</div>
        </div>

        {{-- Items --}}
        @foreach($penjualan->details as $detail)
            <div class="item-row">
                <div class="col-barang">{{ $detail->obat->nama_obat ?? '-' }}</div>
                <div class="col-qty">{{ $detail->jumlah }}</div>
                <div class="col-harga">{{ number_format($detail->harga_satuan, 0, ',', '.') }}</div>
                <div class="col-sub">{{ number_format($detail->subtotal, 0, ',', '.') }}</div>
            </div>
        @endforeach

        <div class="divider"></div>

        {{-- Totals --}}
        <div class="total-row grand-total">
            <span>TOTAL</span>
            <span>{{ number_format($penjualan->total_harga, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Bayar</span>
            <span>{{ number_format($penjualan->nominal_bayar, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Kembalian</span>
            <span>{{ number_format($penjualan->kembalian, 0, ',', '.') }}</span>
        </div>

        <div class="divider"></div>

        {{-- Footer --}}
        <div class="footer-note">
            <div>Terima Kasih telah berbelanja!</div>
            <div style="margin-top: 2px;">Barang yang sudah dibeli tidak dapat dikembalikan</div>
        </div>
    </div>

    <script>
        // Auto trigger print dialog saat halaman dibuka di tab baru
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 300);
        });
    </script>
</body>

</html>