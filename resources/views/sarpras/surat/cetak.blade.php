<!DOCTYPE html>
<html>
<head>
    <title>Surat Keluar - {{ $surat->nomor_surat }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; margin: 50px; }
        .kop-surat { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h1 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .kop-surat p { margin: 2px 0; font-size: 10pt; }
        .nomor-surat { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 20px; }
        .content { margin-top: 20px; min-height: 300px; }
        .footer { margin-top: 50px; float: right; width: 250px; text-align: center; }
        .signature-space { height: 80px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #000; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <div class="kop-surat">
        <h1>SISTEM INFORMASI AKADEMIK (SIAKAD)</h1>
        <p>Jl. Kampus No. 123, Kota Pendidikan</p>
        <p>Telp: (021) 1234567 | Website: www.siakad.ac.id</p>
    </div>

    <div class="nomor-surat">
        SURAT {{ strtoupper($surat->kategori->nama) }}<br>
        Nomor: {{ $surat->nomor_surat }}
    </div>

    <table style="border: none !important; margin-bottom: 20px;">
        <tr style="border: none !important;">
            <td style="border: none !important; width: 100px;">Perihal</td>
            <td style="border: none !important;">: {{ $surat->perihal }}</td>
        </tr>
        <tr style="border: none !important;">
            <td style="border: none !important;">Kepada</td>
            <td style="border: none !important;">: {{ $surat->tujuan }}</td>
        </tr>
        <tr style="border: none !important;">
            <td style="border: none !important;">Tanggal</td>
            <td style="border: none !important;">: {{ $surat->tanggal_surat->format('d F Y') }}</td>
        </tr>
    </table>

    <div class="content">
        @php
            $isi_data = $surat->isi_surat;
            if (is_string($isi_data) && (str_starts_with($isi_data, '{') || str_starts_with($isi_data, '['))) {
                $decoded = json_decode($isi_data, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $isi_data = $decoded;
                }
            }
        @endphp
        @if(is_array($isi_data) && isset($isi_data['barang']))
            <p>Dengan ini menerangkan bahwa:</p>
            <table>
                <tr>
                    <th style="width: 30%;">Nama Barang</th>
                    <td>{{ $isi_data['barang'] }}</td>
                </tr>
                <tr>
                    <th>Jumlah</th>
                    <td>{{ $isi_data['jumlah'] }} unit</td>
                </tr>
                <tr>
                    <th>Tanggal Pinjam</th>
                    <td>{{ $isi_data['tanggal_pinjam'] }}</td>
                </tr>
                <tr>
                    <th>Estimasi Kembali</th>
                    <td>{{ $isi_data['estimasi_kembali'] }}</td>
                </tr>
            </table>
            <p style="margin-top: 20px;">
                Telah diberikan izin untuk meminjam barang tersebut di atas untuk keperluan akademik / operasional kampus.
                Mohon untuk dijaga dan dikembalikan tepat waktu dalam kondisi baik.
            </p>
        @else
            {!! $surat->isi_surat !!}
        @endif
    </div>

    <div class="footer">
        <p>{{ now()->format('d F Y') }}</p>
        <p>Petugas Inventaris,</p>
        <div class="signature-space"></div>
        <p><strong>( ________________ )</strong></p>
    </div>
</body>
</html>
