<table class="ttd">
    <tr>
        <td>
            <div>&nbsp;</div>
            <div>Dibuat oleh,</div>
            <div>Petugas</div>
            <div class="ttd-ruang"></div>
            <div class="ttd-nama">{{ auth()->user()->nama_user ?? '( __________________________ )' }}</div>
        </td>
        <td>
            <div>Blangpidie, {{ now()->locale('id')->translatedFormat('d F Y') }}</div>
            <div>Mengetahui,</div>
            <div>Apoteker Pengelola Apotek</div>
            <div class="ttd-ruang"></div>
            <div>( __________________________ )</div>
        </td>
    </tr>
</table>
