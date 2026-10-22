<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Obat - Apotek Tabah Farma</title>
    @include('pages.laporan.pdf.partials.style')
</head>
<body>
    @include('pages.laporan.pdf.partials.kop', ['namaLaporan' => 'Laporan Stok Obat'])

    <div class="judul">
        <h1>Laporan Stok Obat</h1>
        <p>Per tanggal {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
        @if($statusRop || $search)
            <p class="kecil">
                Filter:
                @if($statusRop) status {{ $statusRop === 'kritis' ? 'perlu pesan ulang' : 'cukup' }}@endif
                @if($statusRop && $search), @endif
                @if($search) kata kunci "{{ $search }}"@endif
            </p>
        @endif
    </div>

    <table class="ringkasan">
        <tr>
            <td class="label">Jumlah jenis obat</td>
            <td class="nilai">{{ $totalObat }}</td>
        </tr>
        <tr>
            <td class="label">Perlu pesan ulang</td>
            <td class="nilai">{{ $totalKritis }}</td>
        </tr>
        <tr>
            <td class="label">Stok cukup</td>
            <td class="nilai">{{ $totalAman }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th class="c" style="width: 24px;">No</th>
                <th>Nama Obat</th>
                <th style="width: 70px;">Kode</th>
                <th style="width: 95px;">Kategori</th>
                <th class="r" style="width: 80px;">Stok Gudang</th>
                <th class="r" style="width: 80px;">Stok Rak</th>
                <th class="r" style="width: 90px;">Total Apotek</th>
                <th class="r" style="width: 100px;">Batas ROP</th>
                <th style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $obat)
                @php
                    $stokGudang = (int) ($obat->stok_gudang_total ?? 0);
                    $stokRak    = (int) ($obat->stok_rak_total ?? 0);
                    $isi        = max((int) ($obat->isi_per_kemasan ?? 1), 1);
                    $total      = $obat->total_apotek_satuan_jual ?? (($stokGudang * $isi) + $stokRak);
                    $rop        = $obat->rop_kalkulasi ?? (int) ($obat->rop_minimum ?? 0);
                    $isKritis   = $obat->is_kritis ?? false;
                @endphp
                <tr>
                    <td class="c">{{ $loop->iteration }}</td>
                    <td class="b">{{ $obat->nama_obat }}</td>
                    <td class="mono">{{ $obat->kode_obat ?? '-' }}</td>
                    <td>{{ $obat->kategori ?? '-' }}</td>
                    <td class="r nowrap">{{ $stokGudang }} {{ $obat->satuan_beli }}</td>
                    <td class="r nowrap">{{ $stokRak }} {{ $obat->satuan_jual }}</td>
                    <td class="r nowrap b">{{ $total }} {{ $obat->satuan_jual }}</td>
                    <td class="r nowrap">
                        {{ $rop }} {{ $obat->satuan_beli }}
                        @if($isi > 1)
                            <div class="kecil">= {{ $rop * $isi }} {{ $obat->satuan_jual }}</div>
                        @endif
                    </td>
                    <td class="{{ $isKritis ? 'tanda' : '' }}">{{ $isKritis ? 'Pesan ulang' : 'Cukup' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="kosong">Tidak ada obat yang cocok dengan filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="keterangan">
        Total Apotek = stok gudang dikali isi per kemasan, ditambah stok rak (dalam satuan jual).
        Status "Pesan ulang" berarti Total Apotek sudah sama dengan atau di bawah Batas ROP.
    </p>

    @include('pages.laporan.pdf.partials.ttd')
</body>
</html>
