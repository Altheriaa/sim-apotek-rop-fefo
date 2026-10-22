<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Transfer Stok ke Rak - Apotek Tabah Farma</title>
    @include('pages.laporan.pdf.partials.style')
</head>
<body>
    @include('pages.laporan.pdf.partials.kop', ['namaLaporan' => 'Laporan Transfer Stok ke Rak'])

    <div class="judul">
        <h1>Laporan Transfer Stok Gudang ke Rak</h1>
        <p>
            @if($tanggalDari && $tanggalSampai)
                Periode {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}
            @else
                Semua tanggal
            @endif
        </p>
    </div>

    <table class="ringkasan">
        <tr>
            <td class="label">Jumlah transfer</td>
            <td class="nilai">{{ number_format($totalTransfer, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Keluar dari gudang</td>
            <td class="nilai">{{ number_format($totalSatuanBeli, 0, ',', '.') }} sat. beli</td>
        </tr>
        <tr>
            <td class="label">Masuk ke rak</td>
            <td class="nilai">{{ number_format($totalSatuanJual, 0, ',', '.') }} sat. jual</td>
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
                <th class="r" style="width: 85px;">Keluar Gudang</th>
                <th class="r" style="width: 85px;">Masuk Rak</th>
                <th style="width: 85px;">Petugas</th>
                <th style="width: 130px;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td class="c">{{ $index + 1 }}</td>
                    <td class="nowrap">{{ \Carbon\Carbon::parse($row->tanggal_transfer)->format('d/m/Y') }}</td>
                    <td class="b">{{ $row->obat->nama_obat ?? '-' }}</td>
                    <td class="mono">{{ $row->obatBatch->nomor_batch ?? '-' }}</td>
                    <td class="c nowrap">{{ $row->obatBatch ? $row->obatBatch->tanggal_kadaluwarsa->format('d/m/Y') : '-' }}</td>
                    <td class="r nowrap">{{ $row->jumlah_keluar }} {{ $row->obat->satuan_beli ?? '' }}</td>
                    <td class="r nowrap">{{ $row->jumlah_masuk_rak }} {{ $row->obat->satuan_jual ?? '' }}</td>
                    <td>{{ $row->user->nama_user ?? '-' }}</td>
                    <td>{{ $row->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="kosong">Tidak ada transfer ke rak pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pages.laporan.pdf.partials.ttd')
</body>
</html>
