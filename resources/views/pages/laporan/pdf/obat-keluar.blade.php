<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Obat Keluar</title>
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
        <h2>LAPORAN OBAT KELUAR</h2>
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
                <th>Petugas</th>
                <th>Jumlah Keluar</th>
                <th>Batch FEFO</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal_keluar)->format('d/m/Y') }}</td>
                    <td>{{ $row->obat->nama_obat }}</td>
                    <td>{{ $row->user->nama_user ?? '-' }}</td>
                    <td class="text-right">{{ $row->jumlah }}</td>
                    <td>{{ $row->obatBatch->nomor_batch ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data pada rentang tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
