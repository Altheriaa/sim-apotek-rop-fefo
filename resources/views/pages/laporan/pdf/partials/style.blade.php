<style>
    @page {
        margin: 12mm 14mm 18mm 14mm;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: Helvetica, Arial, sans-serif;
        font-size: 9pt;
        color: #1f2937;
        line-height: 1.35;
    }

    /* Kop surat */
    .kop {
        width: 100%;
        border-collapse: collapse;
        border-bottom: 3px double #111827;
        margin-bottom: 10px;
    }

    .kop td {
        vertical-align: middle;
        padding: 0 0 6px 0;
    }

    .kop-logo {
        width: 70px;
    }

    .kop-logo img {
        max-height: 58px;
        max-width: 64px;
    }

    .kop-text {
        text-align: center;
        padding-right: 70px;
    }

    .kop-nama {
        font-size: 15pt;
        font-weight: bold;
        color: #111827;
    }

    .kop-alamat {
        font-size: 9pt;
        color: #374151;
        margin-top: 2px;
    }

    .kop-izin {
        font-size: 8pt;
        color: #4b5563;
        margin-top: 1px;
    }

    /* Judul laporan */
    .judul {
        text-align: center;
        margin-bottom: 10px;
    }

    .judul h1 {
        font-size: 12pt;
        font-weight: bold;
        color: #111827;
        text-transform: uppercase;
    }

    .judul p {
        font-size: 9pt;
        color: #374151;
        margin-top: 2px;
    }

    /* Ringkasan: tabel label/nilai biasa, bukan kartu */
    .ringkasan {
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    .ringkasan td {
        border: 1px solid #d1d5db;
        padding: 4px 8px;
        font-size: 8.5pt;
    }

    .ringkasan .label {
        background-color: #f3f4f6;
        color: #374151;
        width: 170px;
    }

    .ringkasan .nilai {
        font-weight: bold;
        color: #111827;
        width: 130px;
        text-align: right;
    }

    /* Tabel data */
    .data {
        width: 100%;
        border-collapse: collapse;
    }

    .data th,
    .data td {
        border: 1px solid #d1d5db;
        padding: 4px 6px;
        font-size: 8.5pt;
        vertical-align: top;
    }

    .data th {
        background-color: #f3f4f6;
        color: #111827;
        font-weight: bold;
        text-align: left;
    }

    .data tbody tr:nth-child(even) td {
        background-color: #fafafa;
    }

    .data tfoot td {
        background-color: #f3f4f6;
        font-weight: bold;
        border-top: 1.5px solid #6b7280;
    }

    .c { text-align: center; }
    .r { text-align: right; }
    .nowrap { white-space: nowrap; }
    .mono { font-family: Courier, monospace; }
    .b { font-weight: bold; }

    .kecil {
        font-size: 7.5pt;
        color: #6b7280;
    }

    /* Satu-satunya warna aksen: menandai baris yang butuh tindakan (expired, perlu pesan ulang) */
    .tanda {
        color: #991b1b;
        font-weight: bold;
    }

    .kosong {
        text-align: center;
        padding: 16px;
        color: #6b7280;
    }

    .keterangan {
        font-size: 8pt;
        color: #4b5563;
        margin-top: 6px;
    }

    /* Tanda tangan */
    .ttd {
        width: 100%;
        border-collapse: collapse;
        margin-top: 18px;
        page-break-inside: avoid;
    }

    .ttd td {
        width: 50%;
        text-align: center;
        vertical-align: top;
        font-size: 9pt;
        padding: 0;
    }

    .ttd-ruang {
        height: 52px;
    }

    .ttd-nama {
        font-weight: bold;
        text-decoration: underline;
    }

    /* Footer tiap halaman */
    .footer {
        position: fixed;
        bottom: -11mm;
        left: 0;
        right: 0;
        height: 6mm;
        border-top: 1px solid #e5e7eb;
        padding-top: 3px;
        font-size: 7.5pt;
        color: #6b7280;
    }

    .footer table {
        width: 100%;
        border-collapse: collapse;
    }

    .footer td {
        padding: 0;
    }

    .halaman:after {
        content: "Hal. " counter(page) " dari " counter(pages);
    }
</style>
