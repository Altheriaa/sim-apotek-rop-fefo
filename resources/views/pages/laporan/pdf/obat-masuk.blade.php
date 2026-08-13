<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Obat Masuk</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; font-size: 10px; color: #555; }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN OBAT MASUK</h2>
        @if($tanggalDari && $tanggalSampai)
            <p>Periode: {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</p>
        @else
            <p>Semua Data</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Obat</th>
                <th>Supplier</th>
                <th>No. Batch</th>
                <th>Tgl. ED</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal_masuk)->format('d/m/Y') }}</td>
                    <td>{{ $row->obat->nama_obat }}</td>
                    <td>{{ $row->supplier->nama_supplier ?? '-' }}</td>
                    <td>{{ $row->nomor_batch ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal_kadaluwarsa)->format('d/m/Y') }}</td>
                    <td class="text-right">{{ $row->stok_awal }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data pada rentang tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
