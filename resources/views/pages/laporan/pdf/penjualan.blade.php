<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan - Apotek Tabah Farma</title>
    @include('pages.laporan.pdf.partials.style')
</head>
<body>
    @include('pages.laporan.pdf.partials.kop', ['namaLaporan' => 'Laporan Penjualan'])

    <div class="judul">
        <h1>Laporan Penjualan</h1>
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
            <td class="label">Jumlah transaksi</td>
            <td class="nilai">{{ number_format($totalTransaksi, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Omzet</td>
            <td class="nilai">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Modal (HPP)</td>
            <td class="nilai">Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Laba kotor (margin {{ $marginPersen }}%)</td>
            <td class="nilai">Rp {{ number_format($totalLaba, 0, ',', '.') }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th class="c" style="width: 24px;">No</th>
                <th style="width: 105px;">No. Transaksi</th>
                <th style="width: 80px;">Waktu</th>
                <th style="width: 85px;">Pembeli</th>
                <th>Item Terjual</th>
                <th style="width: 70px;">Kasir</th>
                <th class="r" style="width: 85px;">Omzet</th>
                <th class="r" style="width: 85px;">HPP</th>
                <th class="r" style="width: 85px;">Laba</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $trx)
                @php
                    $trxHpp  = (float) $trx->total_hpp;
                    $trxLaba = (float) ($trx->total_harga - $trxHpp);
                @endphp
                <tr>
                    <td class="c">{{ $index + 1 }}</td>
                    <td class="mono">{{ $trx->no_transaksi }}</td>
                    <td class="nowrap">{{ $trx->tanggal_transaksi ? $trx->tanggal_transaksi->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $trx->nama_pembeli ?: 'Umum' }}</td>
                    <td>
                        @foreach($trx->details as $item)
                            <div>
                                {{ $item->obat->nama_obat ?? '-' }}:
                                {{ $item->jumlah }} {{ $item->obat->satuan_jual ?? '' }} &times; {{ number_format($item->harga_satuan, 0, ',', '.') }}
                            </div>
                        @endforeach
                    </td>
                    <td>{{ $trx->user->nama_user ?? '-' }}</td>
                    <td class="r nowrap">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                    <td class="r nowrap">Rp {{ number_format($trxHpp, 0, ',', '.') }}</td>
                    <td class="r nowrap">Rp {{ number_format($trxLaba, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="kosong">Tidak ada transaksi pada periode atau filter ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if($data->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="6" class="r">Total</td>
                    <td class="r nowrap">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                    <td class="r nowrap">Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
                    <td class="r nowrap">Rp {{ number_format($totalLaba, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <p class="keterangan">Laba = omzet dikurangi HPP batch FEFO yang terjual. Belum dikurangi biaya operasional.</p>

    @include('pages.laporan.pdf.partials.ttd')
</body>
</html>
