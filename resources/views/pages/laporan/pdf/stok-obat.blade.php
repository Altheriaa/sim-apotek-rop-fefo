<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Obat - Apotek Tabah Farma</title>
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
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.35;
        }

        /* Kop Surat */
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
            font-size: 15pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .kop-sub {
            font-size: 8.5pt;
            color: #334155;
            margin-bottom: 2px;
        }

        .kop-meta {
            font-size: 8pt;
            color: #64748b;
        }

        /* Header Laporan */
        .report-header {
            text-align: center;
            margin-bottom: 12px;
        }

        .report-title {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .report-period {
            font-size: 8.5pt;
            color: #475569;
        }

        .report-filter {
            font-size: 8pt;
            color: #2563eb;
            font-style: italic;
            margin-top: 2px;
        }

        /* Summary Stats */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 12px;
        }

        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px 10px;
            text-align: center;
            width: 33.33%;
        }

        .summary-card.warning {
            background-color: #fffbeb;
            border-color: #fde68a;
        }

        .summary-card.success {
            background-color: #f0fdf4;
            border-color: #bbf7d0;
        }

        .summary-label {
            font-size: 7.5pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .summary-card.warning .summary-label {
            color: #b45309;
        }

        .summary-card.success .summary-label {
            color: #15803d;
        }

        .summary-value {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
        }

        .summary-card.warning .summary-value {
            color: #b45309;
        }

        .summary-card.success .summary-value {
            color: #15803d;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            font-size: 8pt;
        }

        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
            text-align: center;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }

        /* Signatures */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            page-break-inside: avoid;
        }

        .signature-table td {
            border: none;
            text-align: center;
            width: 33.33%;
            vertical-align: top;
            font-size: 8.5pt;
        }

        .sig-space {
            height: 48px;
        }

        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }

        .sig-role {
            font-size: 8pt;
            color: #475569;
            margin-top: 2px;
        }

        /* Footer */
        .page-footer {
            margin-top: 12px;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            font-size: 7.5pt;
            color: #94a3b8;
            display: table;
            width: 100%;
        }

        .page-footer-left {
            display: table-cell;
            text-align: left;
        }

        .page-footer-right {
            display: table-cell;
            text-align: right;
        }
    </style>
</head>
<body>
    <!-- Kop Surat Resmi -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo Apotek">
                @endif
            </td>
            <td class="kop-text">
                <div class="kop-title">APOTEK TABAH FARMA</div>
                <div class="kop-sub">SIA: 503/001/SIA/DPMPTSP-PP/2023 &bull; SIPA: 19850412/SIPA-11.08/2021/2001</div>
                <div class="kop-meta">Jl. Banda Aceh - Medan Km. 17.5, Sukadamai, Aceh Besar &bull; Telp: (0651) 8012345 &bull; Email: apotek.tabahfarma@gmail.com</div>
            </td>
        </tr>
    </table>

    <!-- Header Laporan -->
    <div class="report-header">
        <div class="report-title">LAPORAN STOK OBAT &amp; ROP (GUDANG &amp; DISPLAY RAK)</div>
        <div class="report-period">
            Dicetak pada: <strong>{{ now()->translatedFormat('d F Y, H:i') }} WIB</strong>
            @if($statusRop)
                &bull; Status Filter: <strong>{{ $statusRop === 'kritis' ? 'Kritis (<= ROP)' : 'Aman (> ROP)' }}</strong>
            @endif
        </div>
        @if($search)
            <div class="report-filter">Pencarian: "{{ $search }}"</div>
        @endif
    </div>

    <!-- Summary Box -->
    <table class="summary-table">
        <tr>
            <td class="summary-card">
                <div class="summary-label">Total Jenis Obat</div>
                <div class="summary-value">{{ $totalObat }} <span style="font-size:8pt;font-weight:normal;">obat</span></div>
            </td>
            <td class="summary-card warning">
                <div class="summary-label">Stok Kritis (&le; ROP)</div>
                <div class="summary-value">{{ $totalKritis }} <span style="font-size:8pt;font-weight:normal;">obat</span></div>
            </td>
            <td class="summary-card success">
                <div class="summary-label">Stok Aman (&gt; ROP)</div>
                <div class="summary-value">{{ $totalAman }} <span style="font-size:8pt;font-weight:normal;">obat</span></div>
            </td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th style="text-align: left;">Nama Obat</th>
                <th style="width: 70px;">Kode</th>
                <th style="width: 90px; text-align: left;">Kategori</th>
                <th style="width: 80px;">Stok Gudang</th>
                <th style="width: 80px;">Stok Rak</th>
                <th style="width: 90px;">Total Apotek</th>
                <th style="width: 90px;">Batas ROP</th>
                <th style="width: 85px;">Status ROP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $obat)
                @php
                    $stokGudang = (int) ($obat->stok_gudang_total ?? 0);
                    $stokRak    = (int) ($obat->stok_rak_total ?? 0);
                    $isi        = max((int) ($obat->isi_per_kemasan ?? 1), 1);
                    $totalApotekSatuanJual = $obat->total_apotek_satuan_jual ?? (($stokGudang * $isi) + $stokRak);
                    $rop        = $obat->rop_kalkulasi ?? (int) ($obat->rop_minimum ?? 2);
                    $isKritis   = $obat->is_kritis ?? false;
                @endphp
                <tr style="{{ $isKritis ? 'background-color: #fffdf5;' : '' }}">
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td><strong>{{ $obat->nama_obat }}</strong></td>
                    <td style="text-align: center; font-family: monospace;">{{ $obat->kode_obat ?? '-' }}</td>
                    <td>{{ $obat->kategori ?? '-' }}</td>
                    <td style="text-align: center;">
                        {{ $stokGudang }} {{ $obat->satuan_beli }}
                    </td>
                    <td style="text-align: center;">
                        {{ $stokRak }} {{ $obat->satuan_jual }}
                    </td>
                    <td style="text-align: center; font-weight: bold; color: {{ $isKritis ? '#b45309' : '#0f172a' }};">
                        {{ $totalApotekSatuanJual }} {{ $obat->satuan_jual }}
                    </td>
                    <td style="text-align: center;">
                        {{ $rop }} {{ $obat->satuan_beli }}
                        @if($isi > 1)
                            <div style="font-size: 7pt; color: #64748b;">(= {{ $rop * $isi }} {{ $obat->satuan_jual }})</div>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($isKritis)
                            <span class="badge badge-warning">Kritis (&le; ROP)</span>
                        @else
                            <span class="badge badge-success">Aman</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px; color: #64748b;">
                        Tidak ada data stok obat yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Lembar Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td>
                Petugas Pemeriksa
                <div class="sig-space"></div>
                <div class="sig-name">{{ auth()->user()->nama_user ?? 'Petugas Apotek' }}</div>
                <div class="sig-role">Staf Inventaris &amp; Kasir</div>
            </td>
            <td>
                Apoteker Pengelola Apotek (APA)
                <div class="sig-space"></div>
                <div class="sig-name">apt. Muhammad Altheria, S.Farm</div>
                <div class="sig-role">SIPA. 19850412/SIPA-11.08/2021/2001</div>
            </td>
            <td>
                Pemilik Sarana Apotek (PSA)
                <div class="sig-space"></div>
                <div class="sig-name">Pimpinan Apotek Tabah Farma</div>
                <div class="sig-role">Penanggung Jawab</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="page-footer">
        <div class="page-footer-left">
            SIM Inventaris ROP &amp; FEFO &bull; Apotek Tabah Farma
        </div>
        <div class="page-footer-right">
            Dokumen resmi inventaris apotek
        </div>
    </div>
</body>
</html>
