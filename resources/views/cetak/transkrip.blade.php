<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Transkrip Nilai Sementara - {{ $siswa->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            color: #000;
            padding: 30px 40px;
        }

        .kop {
            display: table;
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .kop-logo {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
            text-align: center;
        }

        .kop-logo img {
            width: 70px;
        }

        .kop-teks {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .kop-teks .inst {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .judul-dok {
            text-align: center;
            margin: 15px 0;
        }

        .judul-dok h2 {
            font-size: 12pt;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .info-mhs {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-mhs td {
            padding: 2px 0;
        }

        .tabel-nilai {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .tabel-nilai th {
            border: 1px solid #000;
            padding: 5px;
            background: #eee;
        }

        .tabel-nilai td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .summary {
            margin-top: 15px;
            width: 100%;
            border: 1px solid #000;
            padding: 8px;
        }

        .ttd-area {
            margin-top: 30px;
            width: 100%;
            display: table;
        }

        .ttd-box {
            display: table-cell;
            width: 50%;
            text-align: center;
        }

        .ttd-line {
            margin-top: 60px;
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 180px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="kop">
        <div class="kop-logo">
            <img src="{{ public_path('logokampus.jpg') }}" alt="Logo">
        </div>
        <div class="kop-teks">
            <div class="inst">{{ config('app.name') }}</div>
            <div>TRANSKRIP NILAI AKADEMIK (SEMENTARA)</div>
        </div>
    </div>

    <table class="info-mhs">
        <tr>
            <td width="100">Nama</td>
            <td width="10">:</td>
            <td><strong>{{ $siswa->nama }}</strong></td>
            <td width="100">NIM</td>
            <td width="10">:</td>
            <td>{{ $siswa->riwayatPendidikanAktif?->nomor_induk }}</td>
        </tr>
        <td>Program Studi</td>
        <td>:</td>
        <td>{{ $siswa->riwayatPendidikanAktif?->jurusan?->nama }}</td>
        <td></td>
        <td></td>
        <td></td>
        </tr>
    </table>

    <table class="tabel-nilai">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="80">Kode</th>
                <th>Mata Kuliah</th>
                <th width="40">SKS</th>
                <th width="40">Nilai</th>
                <th width="40">Bobot</th>
                <th width="60">SKS x B</th>
            </tr>
        </thead>
        <tbody>
            @php $totalSksRow = 0; $totalSkxBobot = 0; @endphp
            @foreach($allLjk as $i => $ljk)
            @php
            $sks = $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->bobot ?? 0;
            $huruf = $ljk->Nilai_Huruf ?? '-';
            $bobot = $ljk->bobot;
            $val = $sks * $bobot;
            $totalSksRow += $sks;
            $totalSkxBobot += $val;
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="text-center">{{ $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->kode_feeder }}</td>
                <td>{{ $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->nama }}</td>
                <td class="text-center">{{ $sks }}</td>
                <td class="text-center">{{ $huruf }}</td>
                <td class="text-center">{{ number_format($bobot, 1) }}</td>
                <td class="text-center">{{ number_format($val, 1) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td width="150">Total SKS Akumulatif</td>
                <td width="10">:</td>
                <td>{{ $totalSksRow }} SKS</td>
            </tr>
            <tr>
                <td>Indeks Prestasi Kumulatif (IPK)</td>
                <td>:</td>
                <td><strong>{{ number_format($ipk, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="ttd-area">
        <div class="ttd-box">
            Mengetahui,<br>
            Ketua Program Studi
            <br><br><br><br>
            <span class="ttd-line">{{ $kaprodi?->nama ?? '____________________' }}</span><br>
            NIDN. {{ $kaprodi?->NIPDN ?? '__________' }}
        </div>
        <div class="ttd-box">
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
            Mahasiswa,
            <br><br><br><br>
            <span class="ttd-line">{{ $siswa->nama }}</span><br>
            NIM. {{ $siswa->riwayatPendidikanAktif?->nomor_induk }}
        </div>
    </div>

</body>

</html>