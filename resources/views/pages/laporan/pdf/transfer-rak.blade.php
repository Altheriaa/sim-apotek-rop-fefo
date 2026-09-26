<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transfer ke Rak</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a2e; margin: 0; padding: 0; }
        .page { padding: 20px 25px; }
        .header { display: flex; align-items: center; border-bottom: 2px solid #3b82f6; padding-bottom: 12px; margin-bottom: 16px; }
        .header-logo { margin-right: 14px; }
        .header-logo img { height: 50px; }
        .header-title h2 { margin: 0; font-size: 16px; color: #1e40af; }
        .header-title p { margin: 3px 0 0; font-size: 10px; color: #6b7280; }
        .meta { display: flex; gap: 20px; margin-bottom: 14px; font-size: 10px; color: #6b7280; }
        .meta span strong { color: #374151; }
        .summary { display: flex; gap: 12px; margin-bottom: 16px; }
        .summary-card { flex: 1; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px 12px; }
        .summary-card .label { font-size: 9px; color: #3b82f6; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-card .value { font-size: 18px; font-weight: 700; color: #1e40af; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { background: #1e40af; }
        thead th { padding: 8px 10px; text-align: left; font-size: 10px; color: #fff; font-weight: 600; }
        tbody tr:nth-child(even) { background: #f8faff; }
        tbody tr:nth-child(odd) { background: #fff; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; font-size: 10px; color: #374151; }
        .badge-green { color: #16a34a; font-weight: 600; }
        .badge-blue { color: #2563eb; font-weight: 600; }
        .footer { margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 8px; font-size: 9px; color: #9ca3af; display: flex; justify-content: space-between; }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="header-logo">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @endif
            </div>
            <div class="header-title">
                <h2>LAPORAN TRANSFER STOK KE RAK</h2>
                <p>Perpindahan stok dari Gudang ke Display Rak menggunakan metode FEFO</p>
                @if($tanggalDari && $tanggalSampai)
                    <p>Periode: <strong>{{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</strong></p>
                @else
                    <p>Periode: <strong>Semua Data</strong></p>
                @endif
            </div>
        </div>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-card">
                <div class="label">Total Transfer</div>
                <div class="value">{{ $totalTransfer }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Total Keluar Gudang</div>
                <div class="value">{{ number_format($totalSatuanBeli) }} <span style="font-size:12px;font-weight:400;color:#6b7280;">sat. beli</span></div>
            </div>
            <div class="summary-card">
                <div class="label">Total Masuk Rak</div>
                <div class="value">{{ number_format($totalSatuanJual) }} <span style="font-size:12px;font-weight:400;color:#6b7280;">sat. jual</span></div>
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Obat</th>
                    <th>No. Batch</th>
                    <th>ED Batch</th>
                    <th>Keluar Gudang</th>
                    <th>Masuk Rak</th>
                    <th>Petugas</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->tanggal_transfer)->format('d/m/Y') }}</td>
                        <td><strong>{{ $row->obat->nama_obat ?? '-' }}</strong></td>
                        <td class="badge-blue">{{ $row->obatBatch->nomor_batch ?? '-' }}</td>
                        <td>{{ $row->obatBatch ? $row->obatBatch->tanggal_kadaluwarsa->format('d/m/Y') : '-' }}</td>
                        <td class="badge-blue">{{ $row->jumlah_keluar }} {{ $row->obat->satuan_beli ?? '' }}</td>
                        <td class="badge-green">{{ $row->jumlah_masuk_rak }} {{ $row->obat->satuan_jual ?? '' }}</td>
                        <td>{{ $row->user->nama_user ?? '-' }}</td>
                        <td>{{ $row->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center; color:#9ca3af; padding: 20px;">
                            Tidak ada data transfer rak pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <span>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</span>
            <span>Sistem Inventaris Apotek | ROP &amp; FEFO</span>
        </div>
    </div>
</body>
</html>
