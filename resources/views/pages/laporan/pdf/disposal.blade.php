<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Disposal / Pembuangan Obat</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a2e; margin: 0; padding: 0; }
        .page { padding: 20px 25px; }
        .header { display: flex; align-items: center; border-bottom: 2px solid #ef4444; padding-bottom: 12px; margin-bottom: 16px; }
        .header-logo { margin-right: 14px; }
        .header-logo img { height: 50px; }
        .header-title h2 { margin: 0; font-size: 16px; color: #b91c1c; }
        .header-title p { margin: 3px 0 0; font-size: 10px; color: #6b7280; }
        .summary { display: flex; gap: 12px; margin-bottom: 16px; }
        .summary-card { flex: 1; border-radius: 8px; padding: 10px 12px; }
        .summary-card.red { background: #fef2f2; border: 1px solid #fecaca; }
        .summary-card.orange { background: #fff7ed; border: 1px solid #fed7aa; }
        .summary-card.gray { background: #f9fafb; border: 1px solid #e5e7eb; }
        .summary-card .label { font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-card.red .label { color: #ef4444; }
        .summary-card.orange .label { color: #f97316; }
        .summary-card.gray .label { color: #6b7280; }
        .summary-card .value { font-size: 18px; font-weight: 700; margin-top: 2px; }
        .summary-card.red .value { color: #b91c1c; }
        .summary-card.orange .value { color: #c2410c; }
        .summary-card.gray .value { color: #374151; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { background: #7f1d1d; }
        thead th { padding: 8px 9px; text-align: left; font-size: 10px; color: #fff; font-weight: 600; }
        tbody tr:nth-child(even) { background: #fff8f8; }
        tbody tr:nth-child(odd) { background: #fff; }
        tbody td { padding: 7px 9px; border-bottom: 1px solid #e5e7eb; font-size: 10px; color: #374151; }
        .badge { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 9px; font-weight: 700; }
        .badge-expired { background: #fee2e2; color: #b91c1c; }
        .badge-rusak { background: #fff7ed; color: #c2410c; }
        .badge-lainnya { background: #f3f4f6; color: #6b7280; }
        .text-red { color: #ef4444; font-weight: 600; }
        .text-orange { color: #f97316; font-weight: 600; }
        .text-blue { color: #2563eb; }
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
                <h2>LAPORAN DISPOSAL / PEMBUANGAN OBAT</h2>
                <p>Riwayat pembuangan batch obat expired, rusak, atau tidak layak pakai</p>
                @if($tanggalDari && $tanggalSampai)
                    <p>Periode: <strong>{{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</strong>
                        @if($alasanFilter) | Alasan: <strong>{{ $alasanOptions[$alasanFilter] ?? $alasanFilter }}</strong>@endif
                    </p>
                @else
                    <p>Periode: <strong>Semua Data</strong></p>
                @endif
            </div>
        </div>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-card gray">
                <div class="label">Total Record</div>
                <div class="value">{{ $totalRecord }} <span style="font-size:12px;font-weight:400;">kejadian</span></div>
            </div>
            <div class="summary-card orange">
                <div class="label">Dibuang dari Gudang</div>
                <div class="value">{{ number_format($totalGudangDisposal) }} <span style="font-size:12px;font-weight:400;">sat. beli</span></div>
            </div>
            <div class="summary-card red">
                <div class="label">Dibuang dari Rak</div>
                <div class="value">{{ number_format($totalRakDisposal) }} <span style="font-size:12px;font-weight:400;">sat. jual</span></div>
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
                    <th>Exp. Date</th>
                    <th>Buang Gudang</th>
                    <th>Buang Rak</th>
                    <th>Alasan</th>
                    <th>Petugas</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->tanggal_keluar)->format('d/m/Y') }}</td>
                        <td><strong>{{ $row->obat->nama_obat ?? '-' }}</strong></td>
                        <td class="text-blue">{{ $row->obatBatch->nomor_batch ?? '-' }}</td>
                        <td>
                            @if($row->obatBatch)
                                @php $isExpired = $row->obatBatch->tanggal_kadaluwarsa->lt(now()); @endphp
                                <span class="{{ $isExpired ? 'text-red' : '' }}">
                                    {{ $row->obatBatch->tanggal_kadaluwarsa->format('d/m/Y') }}
                                    {{ $isExpired ? '(ED)' : '' }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-orange">
                            {{ $row->jumlah_gudang > 0 ? $row->jumlah_gudang . ' ' . ($row->obat->satuan_beli ?? '') : '—' }}
                        </td>
                        <td class="text-red">
                            {{ $row->jumlah_rak > 0 ? $row->jumlah_rak . ' ' . ($row->obat->satuan_jual ?? '') : '—' }}
                        </td>
                        <td>
                            @php
                                $badgeClass = match($row->alasan) {
                                    'expired' => 'badge-expired',
                                    'rusak'   => 'badge-rusak',
                                    default   => 'badge-lainnya',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $alasanOptions[$row->alasan] ?? $row->alasan }}</span>
                        </td>
                        <td>{{ $row->user->nama_user ?? '-' }}</td>
                        <td>{{ $row->catatan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align:center; color:#9ca3af; padding: 20px;">
                            Tidak ada data disposal pada periode ini.
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
