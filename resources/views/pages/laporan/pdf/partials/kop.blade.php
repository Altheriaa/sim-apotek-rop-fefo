{{-- Footer fixed ditaruh paling awal agar DomPDF mengulanginya di setiap halaman --}}
<div class="footer">
    <table>
        <tr>
            <td>Apotek Tabah Farma &bull; {{ $namaLaporan }}</td>
            <td class="r">
                Dicetak {{ now()->format('d/m/Y H:i') }} oleh {{ auth()->user()->nama_user ?? '-' }} &bull;
                <span class="halaman"></span>
            </td>
        </tr>
    </table>
</div>

<table class="kop">
    <tr>
        <td class="kop-logo">
            @if(!empty($logoBase64))
                <img src="{{ $logoBase64 }}" alt="Logo Apotek Tabah Farma">
            @endif
        </td>
        <td class="kop-text">
            <div class="kop-nama">APOTEK TABAH FARMA</div>
            <div class="kop-alamat">Jl. H. Ilyas No. 72, Blangpidie, Kabupaten Aceh Barat Daya, Aceh</div>
            <div class="kop-izin">SIA: 503/024/SIA/DPMTSP/2023 &bull; SIPA: 19920815/SIPA/2022 &bull; Telp / WhatsApp: 0852-6028-0909</div>
        </td>
    </tr>
</table>
