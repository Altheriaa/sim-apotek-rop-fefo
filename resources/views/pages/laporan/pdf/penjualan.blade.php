<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan & Laba - Apotek Tabah Farma</title>
    <style>
        @page {
            margin: 12mm 15mm 15mm 15mm;
            size: a4 landscape;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #1e293b;
            line-height: 1.35;
        }

        /* ── Kop Surat ── */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .kop-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }

        .kop-logo {
            width: 75px;
            text-align: left;
        }

        .kop-logo img {
            max-height: 60px;
            max-width: 70px;
        }

        .kop-text {
            text-align: center;
            padding-right: 75px;
        }

        .kop-title {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .kop-sub {
            font-size: 9pt;
            color: #334155;
            margin-bottom: 2px;
        }

        .kop-meta {
            font-size: 8pt;
            color: #64748b;
        }

        /* ── Title & Filter Header ── */
        .report-header {
            text-align: center;
            margin-bottom: 14px;
        }

        .report-title {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .report-period {
            font-size: 9pt;
            color: #475569;
        }

        .report-filter {
            font-size: 8pt;
            color: #2563eb;
            font-style: italic;
            margin-top: 2px;
        }

        /* ── Summary Stats Box ── */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 14px;
        }

        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px 10px;
            text-align: center;
            width: 25%;
        }

        .summary-label {
            font-size: 8pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .summary-value {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
        }

        .summary-value.highlight-omzet {
            color: #2563eb;
        }

        .summary-value.highlight-hpp {
            color: #475569;
        }

        .summary-value.highlight-laba {
            color: #166534;
        }

        /* ── Data Table ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            font-size: 8.5pt;
        }

        .data-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.3px;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .data-table tfoot th,
        .data-table tfoot td {
            background-color: #e2e8f0;
            font-weight: bold;
            color: #0f172a;
            border-top: 2px solid #94a3b8;
        }

        .item-list {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 8pt;
            line-height: 1.3;
        }

        .item-list li {
            margin-bottom: 2px;
            color: #334155;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-mono { font-family: monospace; font-size: 9pt; }
        .nowrap { white-space: nowrap; }

        /* ── Footer & Signatures ── */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .footer-table td {
            vertical-align: top;
            border: none;
            padding: 0;
            font-size: 9pt;
        }

        .signature-box {
            text-align: center;
            width: 260px;
            float: right;
        }

        .signature-space {
            height: 55px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }

        .signature-role {
            font-size: 8.5pt;
            color: #64748b;
        }

        .meta-info {
            font-size: 8pt;
            color: #64748b;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    {{-- Kop Surat Resmi --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo Apotek">
                @endif
            </td>
            <td class="kop-text">
                <div class="kop-title">APOTEK TABAH FARMA</div>
                <div class="kop-sub">Jl. H. Ilyas No. 72, Blangpidie, Kabupaten Aceh Barat Daya, Aceh</div>
                <div class="kop-meta">SIA: 503/024/SIA/DPMTSP/2023 &bull; SIPA: 19920815/SIPA/2022 &bull; Telp / WhatsApp: 0852-6028-0909</div>
            </td>
        </tr>
    </table>

    {{-- Judul Laporan --}}
    <div class="report-header">
        <div class="report-title">Laporan Penjualan & Laba Keuntungan</div>
        <div class="report-period">
            @if($tanggalDari && $tanggalSampai)
                Periode: <strong>{{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</strong>
            @else
                Periode: <strong>Semua Tanggal</strong>
            @endif
        </div>
        @if(!empty($search))
            <div class="report-filter">Filter kata kunci: "{{ $search }}"</div>
        @endif
    </div>

    {{-- Ringkasan Keuangan (Statistik) --}}
    <table class="summary-table">
        <tr>
            <td class="summary-card">
                <div class="summary-label">Total Transaksi</div>
                <div class="summary-value">{{ number_format($totalTransaksi, 0, ',', '.') }} Transaksi</div>
            </td>
            <td class="summary-card">
                <div class="summary-label">Total Omzet Penjualan</div>
                <div class="summary-value highlight-omzet">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            </td>
            <td class="summary-card">
                <div class="summary-label">Total Modal Pokok (HPP)</div>
                <div class="summary-value highlight-hpp">Rp {{ number_format($totalHpp, 0, ',', '.') }}</div>
            </td>
            <td class="summary-card">
                <div class="summary-label">Total Laba Bersih (+{{ $marginPersen }}%)</div>
                <div class="summary-value highlight-laba">Rp {{ number_format($totalLaba, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    {{-- Tabel Data Transaksi --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th style="width: 105px;">No. Transaksi</th>
                <th style="width: 80px;">Waktu</th>
                <th style="width: 90px;">Pembeli</th>
                <th>Rincian Item Obat Terjual</th>
                <th style="width: 70px;">Kasir</th>
                <th style="width: 85px;" class="text-right">Omzet (Rp)</th>
                <th style="width: 85px;" class="text-right">Modal / HPP (Rp)</th>
                <th style="width: 85px;" class="text-right">Laba (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $trx)
                @php
                    $trxHpp = (float) $trx->total_hpp;
                    $trxLaba = (float) ($trx->total_harga - $trxHpp);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-mono">{{ $trx->no_transaksi }}</td>
                    <td class="nowrap">{{ $trx->tanggal_transaksi ? $trx->tanggal_transaksi->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $trx->nama_pembeli ?: 'Umum' }}</td>
                    <td>
                        <ul class="item-list">
                            @foreach($trx->details as $item)
                                <li>
                                    &bull; <strong>{{ $item->obat->nama_obat ?? 'Obat' }}</strong> 
                                    ({{ $item->jumlah }} {{ $item->obat->satuan_jual ?? 'unit' }} &times; {{ number_format($item->harga_satuan, 0, ',', '.') }})
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td>{{ $trx->user->nama_user ?? '-' }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                    <td class="text-right text-slate-600">Rp {{ number_format($trxHpp, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: #166534;">Rp {{ number_format($trxLaba, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada transaksi penjualan pada rentang tanggal / filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($data->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right uppercase"><strong>Grand Total:</strong></td>
                    <td class="text-right font-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: #166534;">Rp {{ number_format($totalLaba, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- Bagian Tanda Tangan & Footer --}}
    <table class="footer-table">
        <tr>
            <td style="width: 60%;">
                <div class="meta-info">
                    <strong>Catatan:</strong><br>
                    &bull; Laporan ini dicetak secara resmi dari Sistem Informasi Manajemen Apotek Tabah Farma.<br>
                    &bull; Laba dihitung secara otomatis berdasarkan selisih harga jual dengan HPP batch FEFO.<br>
                    &bull; Waktu Cetak: {{ now()->format('d/m/Y H:i:s') }} WIB<br>
                    &bull; Petugas Pencetak: {{ auth()->user()->nama_user ?? 'Administrator' }}
                </div>
            </td>
            <td style="width: 40%;">
                <div class="signature-box">
                    <div>Blangpidie, {{ now()->format('d/m/Y') }}</div>
                    <div>Pemilik / Pimpinan Apotek,</div>
                    <div class="signature-space"></div>
                    <div class="signature-name">( __________________________ )</div>
                    <div class="signature-role">Apoteker Pengelola Apotek (APA)</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
