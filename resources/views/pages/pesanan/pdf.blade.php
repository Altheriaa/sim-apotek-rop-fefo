<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Pesanan - {{ $pesanan->nomor_surat }}</title>
    <style>
        @page {
            margin: 12mm 22mm 12mm 22mm;
            size: a4 portrait;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #000;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }

        /* ── Kop Surat Resmi ── */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .kop-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }

        .kop-logo {
            width: 70px;
            text-align: left;
        }

        .kop-logo img {
            max-height: 58px;
            max-width: 65px;
        }

        .kop-text {
            text-align: center;
            padding-right: 70px; /* Menyeimbangkan logo di kiri */
        }

        .kop-title {
            font-size: 15pt;
            font-weight: bold;
            color: #000;
            letter-spacing: 0.8px;
            margin-bottom: 2px;
        }

        .kop-address {
            font-size: 9.5pt;
            color: #111;
            margin-bottom: 2px;
        }

        .kop-apoteker {
            font-size: 9pt;
            color: #111;
            margin-bottom: 2px;
        }

        .kop-stra {
            font-size: 8.5pt;
            color: #111;
        }

        .kop-divider {
            border-bottom: 2px solid #000;
            margin-top: 8px;
            margin-bottom: 10px;
        }

        /* ── Judul Dokumen ── */
        .doc-title-container {
            text-align: center;
            margin-bottom: 10px;
        }

        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        /* ── Metadata Pesanan (Nomor & Tujuan) ── */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 9pt;
        }

        .meta-table td {
            vertical-align: top;
            border: none;
            padding: 0;
        }

        .meta-left {
            width: 48%;
        }

        .meta-right {
            width: 52%;
            padding-left: 15px;
        }

        .meta-nomor {
            font-size: 9.5pt;
            margin-bottom: 2px;
        }

        .meta-subcode {
            font-size: 8pt;
            color: #475569;
        }

        .supplier-table {
            width: 100%;
            border-collapse: collapse;
        }

        .supplier-table td {
            vertical-align: top;
            border: none;
            padding: 0;
        }

        /* ── Pernyataan Permohonan ── */
        .intro-text {
            font-size: 9pt;
            margin-bottom: 6px;
        }

        /* ── Tabel Daftar Obat ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 4.5px 7px;
            font-size: 8.5pt;
            line-height: 1.25;
        }

        .items-table th {
            background-color: #f8fafc;
            color: #000;
            font-weight: bold;
            text-align: center;
        }

        .col-no {
            width: 8%;
            text-align: center;
        }

        .col-nama {
            width: 62%;
            text-align: left;
        }

        .col-jumlah {
            width: 30%;
            text-align: center;
            font-weight: 500;
        }

        /* ── Catatan Tambahan (Bila ada) ── */
        .notes-box {
            font-size: 8pt;
            color: #334155;
            margin-bottom: 8px;
            padding: 4px 8px;
            border: 1px dashed #94a3b8;
            background-color: #f8fafc;
            border-radius: 4px;
        }

        /* ── Tanda Tangan Penanggung Jawab ── */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            page-break-inside: avoid;
        }

        .footer-table td {
            border: none;
            padding: 0;
        }

        .signature-wrapper {
            width: 230px;
            margin-left: auto;
            text-align: center;
        }

        .signature-space {
            height: 48px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 9pt;
            color: #000;
            text-decoration: underline;
        }

        .signature-stra {
            font-size: 8.5pt;
            color: #000;
            margin-top: 2px;
        }
    </style>
</head>
<body>

    {{-- Kop Surat Resmi Apotek Tabah Farma --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo Apotek">
                @endif
            </td>
            <td class="kop-text">
                <div class="kop-title">APOTEK TABAH FARMA</div>
                <div class="kop-address">Jl. H. Ilyas No.72 Blangpidie</div>
                <div class="kop-apoteker">Apoteker : Apt. Siti Qamaryatul Husna, S.Farm</div>
                <div class="kop-stra">STRA : 022872122-99033107, SIPA : 503-003-SIP.A</div>
            </td>
        </tr>
    </table>

    <div class="kop-divider"></div>

    {{-- Judul Surat --}}
    <div class="doc-title-container">
        <span class="doc-title">SURAT PESANAN</span>
    </div>

    {{-- Informasi Nomor & Tujuan Surat --}}
    <table class="meta-table">
        <tr>
            <td class="meta-left">
                <div class="meta-nomor">
                    <strong>No. &nbsp;</strong> {{ $pesanan->nomor_surat }}
                </div>
                <div class="meta-subcode">
                    Ref Sistem: #{{ $pesanan->kode_pesanan }}
                </div>
            </td>
            <td class="meta-right">
                <table class="supplier-table">
                    <tr>
                        <td style="width: 70px;"><strong>Kepada :</strong></td>
                        <td><strong>{{ $pesanan->supplier->nama_supplier ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td style="padding-top: 3px;"><strong>Yth &nbsp; &nbsp; :</strong></td>
                        <td style="padding-top: 3px; color: #334155;">Bagian Pemesanan / Sales</td>
                    </tr>
                    @if($pesanan->supplier && $pesanan->supplier->alamat)
                        <tr>
                            <td colspan="2" style="padding-top: 2px; font-size: 8.5pt; color: #64748b;">
                                {{ $pesanan->supplier->alamat }}
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- Teks Pembuka --}}
    <div class="intro-text">
        Mohon dikirimkan obat untuk apotek, sbb :
    </div>

    {{-- Tabel Daftar Obat yang Dipesan --}}
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th class="col-nama">Nama Obat</th>
                <th class="col-jumlah">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pesanan->detailPesanan as $index => $detail)
                <tr>
                    <td class="col-no">{{ $index + 1 }}.</td>
                    <td class="col-nama">{{ $detail->obat->nama_obat ?? '-' }}</td>
                    <td class="col-jumlah">
                        {{ $detail->jumlah_pesan }} {{ $detail->obat->satuan_beli ?? 'Box' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #64748b; padding: 15px;">
                        Tidak ada item obat dalam surat pesanan ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Catatan Pesanan (Jika Ada dan Khusus) --}}
    @if($pesanan->catatan && !in_array($pesanan->catatan, ['Pesanan manual', 'Digenerate otomatis oleh sistem ROP (Stok Menipis)']))
        <div class="notes-box">
            <strong>Catatan Tambahan:</strong> {{ $pesanan->catatan }}
        </div>
    @endif

    {{-- Tempat dan Tanggal Serta Tanda Tangan Penanggung Jawab --}}
    <table class="footer-table">
        <tr>
            <td style="width: 45%; vertical-align: bottom; text-align: left;">
                <div style="font-size: 7.5pt; color: #94a3b8; line-height: 1.35;">
                    Surat pesanan resmi Apotek Tabah Farma.<br>
                    Dicetak otomatis pada: {{ now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WIB
                </div>
            </td>
            <td style="width: 55%; vertical-align: top; text-align: right;">
                <div class="signature-wrapper">
                    <div>Blangpidie, {{ \Carbon\Carbon::parse($pesanan->tanggal_pesan)->locale('id')->isoFormat('D MMMM Y') }}</div>
                    <div style="margin-top: 2px;">Penanggung Jawab,</div>
                    <div class="signature-space"></div>
                    <div class="signature-name">Apt. Siti Qamaryatul Husna, S.Farm</div>
                    <div class="signature-stra">STRA : 022872122-99033107</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
