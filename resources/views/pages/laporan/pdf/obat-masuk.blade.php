<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Obat Masuk - Apotek Tabah Farma</title>
    @include('pages.laporan.pdf.partials.style')
</head>
<body>
    @include('pages.laporan.pdf.partials.kop', ['namaLaporan' => 'Laporan Obat Masuk'])

    <div class="judul">
        <h1>Laporan Obat Masuk</h1>
        <p>
            @if($tanggalDari && $tanggalSampai)
                Periode {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}
            @else
                Semua tanggal
            @endif
        </p>
        @if(!empty($search))
            <p class="kecil">Filter: kata kunci "{{ $search }}"</p>
        @endif
    </div>

    <table class="ringkasan">
        <tr>
            <td class="label">Jumlah batch masuk</td>
            <td class="nilai">{{ number_format($totalBatch, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Jumlah supplier</td>
            <td class="nilai">{{ number_format($totalSupplier, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Nilai pembelian</td>
            <td class="nilai">Rp {{ number_format($totalNilaiBeli, 0, ',', '.') }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th class="c" style="width: 24px;">No</th>
                <th style="width: 65px;">Tgl. Masuk</th>
                <th style="width: 90px;">No. Batch</th>
                <th>Nama Obat</th>
                <th style="width: 80px;">Kategori</th>
                <th style="width: 120px;">Supplier</th>
                <th class="c" style="width: 65px;">Tgl. ED</th>
                <th class="r" style="width: 70px;">Jumlah</th>
                <th class="r" style="width: 80px;">Harga Satuan</th>
                <th class="r" style="width: 90px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotalQty  = 0;
                $grandTotalBeli = 0;
            @endphp
            @forelse($data as $index => $row)
                @php
                    $qty         = (int) $row->stok_gudang;
                    $hargaSatuan = (float) ($row->harga_beli_satuan ?? 0);
                    $subtotal    = (float) ($row->harga_beli && $row->harga_beli > 0 ? $row->harga_beli : ($qty * $hargaSatuan));
                    $grandTotalQty  += $qty;
                    $grandTotalBeli += $subtotal;
                @endphp
                <tr>
                    <td class="c">{{ $index + 1 }}</td>
                    <td class="nowrap">{{ \Carbon\Carbon::parse($row->tanggal_masuk)->format('d/m/Y') }}</td>
                    <td class="mono">{{ $row->nomor_batch ?? '-' }}</td>
                    <td class="b">{{ $row->obat->nama_obat ?? '-' }}</td>
                    <td>{{ $row->obat->kategori ?? '-' }}</td>
                    <td>{{ $row->supplier->nama_supplier ?? '-' }}</td>
                    <td class="c nowrap">{{ $row->tanggal_kadaluwarsa ? \Carbon\Carbon::parse($row->tanggal_kadaluwarsa)->format('d/m/Y') : '-' }}</td>
                    <td class="r nowrap">{{ number_format($qty, 0, ',', '.') }} {{ $row->obat->satuan_beli ?? '' }}</td>
                    <td class="r nowrap">Rp {{ number_format($hargaSatuan, 0, ',', '.') }}</td>
                    <td class="r nowrap">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="kosong">Tidak ada obat masuk pada periode atau filter ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if($data->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="7" class="r">Total</td>
                    <td class="r">{{ number_format($grandTotalQty, 0, ',', '.') }}</td>
                    <td></td>
                    <td class="r nowrap">Rp {{ number_format($grandTotalBeli, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    @include('pages.laporan.pdf.partials.ttd')
</body>
</html>