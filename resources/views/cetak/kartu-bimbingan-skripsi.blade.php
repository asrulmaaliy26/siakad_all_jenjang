<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kartu Bimbingan Sidang Skripsi - {{ $ta->riwayatPendidikan?->siswa?->nama }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #000;
            padding: 30px 40px;
        }

        /* ── KOP SURAT ── */
        .header {
            text-align: center;
            margin-bottom: 5px;
            position: relative;
        }

        .header-logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px;
        }

        .header-logo img {
            width: 80px;
        }

        .header-text {
            padding: 0 90px;
        }

        .header-text h3 {
            font-size: 11pt;
            margin-bottom: 2px;
            font-weight: normal;
        }

        .header-text h2 {
            font-size: 13pt;
            margin-bottom: 2px;
            font-weight: bold;
        }

        .header-text p {
            font-size: 9pt;
            margin-bottom: 2px;
        }

        .line-double {
            border-bottom: 3px solid #000;
            margin-top: 5px;
            position: relative;
        }

        .line-double::after {
            content: "";
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 100%;
            border-bottom: 1px solid #000;
        }

        /* ── JUDUL DOKUMEN ── */
        .doc-title {
            text-align: center;
            margin: 15px 0 10px;
            font-weight: bold;
            font-size: 12pt;
            text-transform: uppercase;
        }

        /* ── INFO TABEL ── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            border: 1px solid #000;
            padding: 6px 10px;
            vertical-align: middle;
        }

        .info-label {
            width: 200px;
        }

        /* ── KONSULTASI TABEL ── */
        .consult-table {
            width: 100%;
            border-collapse: collapse;
        }

        .consult-table th,
        .consult-table td {
            border: 1px solid #000;
            padding: 6px;
        }

        .consult-table th {
            background-color: #fff;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
        }

        .center {
            text-align: center;
        }

        /* ── PAGE BREAK ── */
        .page-break {
            page-break-after: always;
        }

        .last-page .page-break {
            page-break-after: auto;
        }

        /* ── CHAT PARSER STYLING ── */
        .entry-row-content {
            font-size: 9pt;
            line-height: 1.3;
        }
    </style>
</head>

<body>

    @php
    $pembimbings = [];
    if ($ta->id_dosen_pembimbing_1) $pembimbings[] = 1;
    if ($ta->id_dosen_pembimbing_2) $pembimbings[] = 2;
    if ($ta->id_dosen_pembimbing_3) $pembimbings[] = 3;
    $totalP = count($pembimbings);
    @endphp

    @foreach($pembimbings as $idx => $pNum)
    @php
    $dField = "dosenPembimbing{$pNum}";
    $dosen = $ta->$dField;
    $hField = "ctt_revisi_dosen_{$pNum}";
    $historyHtml = $ta->$hField;

    // Regex to extract entries from our custom div structure
    preg_match_all('#<div class=\'mb-2.*?<span.*?>(.*?)</span>.*?<span.*?>(.*?)</span>.*?<div class=\'text-sm.*?\'>(.*?)</div>\s*</div>#si', $historyHtml, $matches, PREG_SET_ORDER);

    $entries = array_reverse($matches);
    @endphp

    <div class="card-section {{ ($idx + 1 == $totalP) ? 'last-page' : '' }}">
        {{-- KOP SURAT --}}
        <div class="header">
            <div class="header-logo">
                <img src="{{ public_path('logokampus.jpg') }}" alt="Logo">
            </div>
            <div class="header-text">
                <h3>MAJELIS PENDIDIKAN TINGGI PENELITIAN DAN PENGEMBANGAN</h3>
                <h2>{{ config('app.name', 'SIAKAD') }}</h2>
                <p>TERAKREDITASI B</p>
                <p>Alamat Institusi Terupdate | Telp: 08XXXXXXXX | Email: admin@siakad.com</p>
            </div>
            <div class="line-double"></div>
        </div>

        <div class="doc-title">KARTU BIMBINGAN SIDANG SKRIPSI</div>

        <table class="info-table">
            <tr>
                <td class="info-label">Nama</td>
                <td>{{ $ta->riwayatPendidikan?->siswa?->nama }}</td>
            </tr>
            <tr>
                <td class="info-label">Program Studi</td>
                <td>{{ $ta->riwayatPendidikan?->jurusan?->nama }}</td>
            </tr>
            <tr>
                <td class="info-label">Judul Skripsi</td>
                <td>{{ $ta->judul }}</td>
            </tr>
            <tr>
                <td class="info-label">Dosen Pembimbing {{ $pNum }}</td>
                <td>{{ $dosen?->nama }}</td>
            </tr>
        </table>

        <table class="consult-table">
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th width="80">Tanggal</th>
                    <th>Keterangan Konsultasi</th>
                    <th width="100">Paraf <br> Pembimbing</th>
                    <th width="100">Tanggal <br> Menghadap <br> Kembali</th>
                </tr>
            </thead>
            <tbody>
                @php $rowNum = 1; @endphp
                @foreach($entries as $entry)
                @php
                $sender = strip_tags($entry[1]);
                $timestamp = strip_tags($entry[2]);
                $content = $entry[3];

                $dateOnly = explode(' ', $timestamp)[0] ?? '-';
                @endphp
                <tr>
                    <td class="center">{{ $rowNum++ }}</td>
                    <td class="center">{{ $dateOnly }}</td>
                    <td>
                        <div class="entry-row-content">
                            <strong>{{ $sender }}:</strong><br>
                            {!! $content !!}
                        </div>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                @endforeach

                @for($i = count($entries); $i < 15; $i++)
                    <tr>
                    <td class="center" style="height: 25px;">{{ $rowNum++ }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    </tr>
                    @endfor
            </tbody>
        </table>

        @if($idx + 1 < $totalP)
            <div class="page-break">
    </div>
    @endif
    </div>
    @endforeach

</body>

</html>