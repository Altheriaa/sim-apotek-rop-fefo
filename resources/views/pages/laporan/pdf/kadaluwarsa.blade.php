<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Obat Kadaluwarsa - Apotek Tabah Farma</title>
    @include('pages.laporan.pdf.partials.style')
</head>
<body>
    @include('pages.laporan.pdf.partials.kop', ['namaLaporan' => 'Laporan Obat Kadaluwarsa'])

    @php
        $now = now()->startOfDay();
        $jumlahLewat  = $data->filter(fn ($b) => $b->tanggal_kadaluwarsa->copy()->startOfDay()->lt($now))->count();
        $jumlahDekat  = $data->count() - $jumlahLewat;

        $labelStatus = [
            'expired'      => 'sudah kadaluwarsa',
            'expiring_30'  => 'ED dalam 30 hari',
            'expiring_90'  => 'ED dalam 90 hari',
            'expiring_180' => 'ED dalam 6 bulan',
        ];
        $filter = array_filter([
            $lokasi ? 'lokasi ' . ($lokasi === 'rak' ? 'display rak' : 'gudang') : null,
            $statusEd ? ($labelStatus[$statusEd] ?? $statusEd) : null,
            $search ? 'kata kunci "' . $search . '"' : null,
        ]);
    @endphp

    <div class="judul">
        <h1>Laporan Obat Kadaluwarsa dan Mendekati Kadaluwarsa</h1>
        <p>Per tanggal {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
        @if(count($filter))
            <p class="kecil">Filter: {{ implode(', ', $filter) }}</p>
        @endif
    </div>

    <table class="ringkasan">
        <tr>
            <td class="label">Sudah kadaluwarsa</td>
            <td class="nilai">{{ $jumlahLewat }} batch</td>
        </tr>
        <tr>
            <td class="label">Mendekati kadaluwarsa</td>
            <td class="nilai">{{ $jumlahDekat }} batch</td>
        </tr>
        <tr>
            <td class="label">Jumlah batch</td>
            <td class="nilai">{{ $data->count() }} batch</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th class="c" style="width: 24px;">No</th>
                <th>Nama Obat</th>
                <th style="width: 90px;">No. Batch</th>
                <th style="width: 130px;">Supplier</th>
                <th class="c" style="width: 70px;">Tgl. ED</th>
                <th style="width: 95px;">Keterangan</th>
                <th class="r" style="width: 75px;">Stok Rak</th>
                <th class="r" style="width: 80px;">Stok Gudang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $batch)
                @php
                    $ed        = $batch->tanggal_kadaluwarsa->copy()->startOfDay();
                    $isExpired = $ed->lt($now);
                    $selisih   = (int) $now->diffInDays($ed, false);
                @endphp
                <tr>
                    <td class="c">{{ $loop->iteration }}</td>
                    <td>
                        <span class="b">{{ $batch->obat->nama_obat ?? '-' }}</span>
                        @if($batch->obat?->kode_obat)
                            <div class="kecil mono">{{ $batch->obat->kode_obat }}</div>
                        @endif
                    </td>
                    <td class="mono">{{ $batch->nomor_batch ?? '-' }}</td>
                    <td>{{ $batch->supplier->nama_supplier ?? '-' }}</td>
                    <td class="c nowrap">{{ $ed->format('d/m/Y') }}</td>
                    <td class="{{ $isExpired ? 'tanda' : '' }}">
                        @if($isExpired)
                            Lewat {{ abs($selisih) }} hari
                        @elseif($selisih === 0)
                            ED hari ini
                        @else
                            Sisa {{ $selisih }} hari
                        @endif
                    </td>
                    <td class="r nowrap">{{ (int) $batch->stok_rak }} {{ $batch->obat->satuan_jual ?? '' }}</td>
                    <td class="r nowrap">{{ (int) $batch->stok_gudang }} {{ $batch->obat->satuan_beli ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="kosong">Tidak ada batch yang kadaluwarsa atau mendekati kadaluwarsa untuk filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="keterangan">
        Diurutkan dari tanggal ED paling awal (FEFO). Tanpa filter status, laporan memuat batch yang sudah lewat ED
        dan yang ED-nya dalam 6 bulan ke depan, selama masih ada stok di rak atau gudang.
    </p>

    @include('pages.laporan.pdf.partials.ttd')
</body>
</html>
