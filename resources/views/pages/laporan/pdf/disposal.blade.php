<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Pembuangan Obat - Apotek Tabah Farma</title>
    @include('pages.laporan.pdf.partials.style')
</head>
<body>
    @include('pages.laporan.pdf.partials.kop', ['namaLaporan' => 'Laporan Pembuangan Obat'])

    <div class="judul">
        <h1>Laporan Pembuangan Obat (Disposal)</h1>
        <p>
            @if($tanggalDari && $tanggalSampai)
                Periode {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}
            @else
                Semua tanggal
            @endif
        </p>
        @if($alasanFilter)
            <p class="kecil">Filter: alasan {{ strtolower($alasanOptions[$alasanFilter] ?? $alasanFilter) }}</p>
        @endif
    </div>

    <table class="ringkasan">
        <tr>
            <td class="label">Jumlah pembuangan</td>
            <td class="nilai">{{ number_format($totalRecord, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Dibuang dari gudang</td>
            <td class="nilai">{{ number_format($totalGudangDisposal, 0, ',', '.') }} sat. beli</td>
        </tr>
        <tr>
            <td class="label">Dibuang dari rak</td>
            <td class="nilai">{{ number_format($totalRakDisposal, 0, ',', '.') }} sat. jual</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th class="c" style="width: 24px;">No</th>
                <th style="width: 65px;">Tanggal</th>
                <th>Nama Obat</th>
                <th style="width: 90px;">No. Batch</th>
                <th class="c" style="width: 65px;">Tgl. ED</th>
                <th class="r" style="width: 80px;">Dari Gudang</th>
                <th class="r" style="width: 80px;">Dari Rak</th>
                <th style="width: 75px;">Alasan</th>
                <th style="width: 80px;">Petugas</th>
                <th style="width: 120px;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td class="c">{{ $index + 1 }}</td>
                    <td class="nowrap">{{ \Carbon\Carbon::parse($row->tanggal_keluar)->format('d/m/Y') }}</td>
                    <td class="b">{{ $row->obat->nama_obat ?? '-' }}</td>
                    <td class="mono">{{ $row->obatBatch->nomor_batch ?? '-' }}</td>
                    <td class="c nowrap">{{ $row->obatBatch ? $row->obatBatch->tanggal_kadaluwarsa->format('d/m/Y') : '-' }}</td>
                    <td class="r nowrap">{{ $row->jumlah_gudang > 0 ? $row->jumlah_gudang . ' ' . ($row->obat->satuan_beli ?? '') : '-' }}</td>
                    <td class="r nowrap">{{ $row->jumlah_rak > 0 ? $row->jumlah_rak . ' ' . ($row->obat->satuan_jual ?? '') : '-' }}</td>
                    <td>{{ $alasanOptions[$row->alasan] ?? $row->alasan }}</td>
                    <td>{{ $row->user->nama_user ?? '-' }}</td>
                    <td>{{ $row->catatan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="kosong">Tidak ada pembuangan obat pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pages.laporan.pdf.partials.ttd')
</body>
</html>
