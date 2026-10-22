<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Obat Kadaluwarsa - Apotek Tabah Farma</title>
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

        /* ── Header Laporan ── */
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

        /* ── Summary Stats ── */
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

        .summary-card.red {
            background-color: #fef2f2;
            border-color: #fca5a5;
        }

        .summary-card.orange {
            background-color: #fff7ed;
            border-color: #fed7aa;
        }

        .summary-label {
            font-size: 7.5pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .summary-card.red .summary-label {
            color: #dc2626;
        }

        .summary-card.orange .summary-label {
            color: #ea580c;
        }

        .summary-value {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
        }

        .summary-card.red .summary-value {
            color: #b91c1c;
        }

        .summary-card.orange .summary-value {
            color: #c2410c;
        }

        /* ── Data Table ── */
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

        .badge-expired {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-expiring {
            background-color: #fef3c7;
            color: #92400e;
        }

        /* ── Signatures ── */
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

        /* ── Footer ── */
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
        <div class="report-title">LAPORAN OBAT KADALUWARSA &amp; MENDEKATI ED</div>
        <div class="report-period">
            Dicetak pada: <strong>{{ now()->translatedFormat('d F Y, H:i') }} WIB</strong>
            @if($lokasi)
                &bull; Lokasi: <strong>{{ $lokasi === 'rak' ? 'Display Rak' : 'Gudang' }}</strong>
            @endif
            @if($statusEd)
                &bull; Status: <strong>{{ $statusEd === 'expired' ? 'Sudah Kadaluwarsa' : 'Mendekati ED' }}</strong>
            @endif
        </div>
        @if($search)
            <div class="report-filter">Pencarian: "{{ $search }}"</div>
        @endif
    </div>

    @php
        $now = now()->startOfDay();
        $expiredCount  = $data->filter(fn($b) => $b->tanggal_kadaluwarsa->startOfDay()->lt($now))->count();
        $expiringCount = $data->filter(fn($b) => $b->tanggal_kadaluwarsa->startOfDay()->gte($now))->count();
        $totalRecord   = $data->count();
    @endphp

    <!-- Summary Box -->
    <table class="summary-table">
        <tr>
            <td class="summary-card red">
                <div class="summary-label">Sudah Kadaluwarsa</div>
                <div class="summary-value">{{ $expiredCount }} <span style="font-size:8pt;font-weight:normal;">batch</span></div>
            </td>
            <td class="summary-card orange">
                <div class="summary-label">Mendekati Kadaluwarsa</div>
                <div class="summary-value">{{ $expiringCount }} <span style="font-size:8pt;font-weight:normal;">batch</span></div>
            </td>
            <td class="summary-card">
                <div class="summary-label">Total Batch Terdata</div>
                <div class="summary-value">{{ $totalRecord }} <span style="font-size:8pt;font-weight:normal;">batch</span></div>
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
                <th style="width: 85px;">No. Batch</th>
                <th style="width: 120px; text-align: left;">Supplier</th>
                <th style="width: 75px;">Tgl. ED (FEFO)</th>
                <th style="width: 75px;">Sisa Waktu</th>
                <th style="width: 70px;">Stok Rak</th>
                <th style="width: 70px;">Stok Gudang</th>
                <th style="width: 90px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $batch)
                @php
                    $ed        = $batch->tanggal_kadaluwarsa;
                    $isExpired = $ed->startOfDay()->lt($now);
                    $daysDiff  = (int) $now->diffInDays($ed->startOfDay(), false);
                    $stokRak   = (int) $batch->stok_rak;
                    $stokGudang = (int) $batch->stok_gudang;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td><strong>{{ $batch->obat->nama_obat ?? '-' }}</strong></td>
                    <td style="text-align: center; font-family: monospace;">{{ $batch->obat->kode_obat ?? '-' }}</td>
                    <td style="text-align: center; font-family: monospace;">{{ $batch->nomor_batch ?? '-' }}</td>
                    <td>{{ $batch->supplier->nama_supplier ?? '-' }}</td>
                    <td style="text-align: center; font-weight: bold; color: {{ $isExpired ? '#b91c1c' : '#1e293b' }};">
                        {{ $ed->format('d/m/Y') }}
                    </td>
                    <td style="text-align: center; font-size: 7.5pt; color: {{ $isExpired ? '#dc2626' : '#64748b' }};">
                        @if($isExpired)
                            Lewat {{ abs($daysDiff) }} hr
                        @elseif($daysDiff === 0)
                            Hari ini
                        @else
                            {{ $daysDiff }} hr lagi
                        @endif
                    </td>
                    <td style="text-align: center;">
                        {{ $stokRak }} {{ $batch->obat->satuan_jual ?? '' }}
                    </td>
                    <td style="text-align: center;">
                        {{ $stokGudang }} {{ $batch->obat->satuan_beli ?? '' }}
                    </td>
                    <td style="text-align: center;">
                        @if($isExpired)
                            <span class="badge badge-expired">Expired</span>
                        @else
                            <span class="badge badge-expiring">Mendekati ED</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px; color: #64748b;">
                        Tidak ada data obat kadaluwarsa atau mendekati ED yang ditemukan.
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
