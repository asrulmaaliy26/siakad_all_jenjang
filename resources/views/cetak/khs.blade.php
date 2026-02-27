<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kartu Hasil Studi - {{ $krs->riwayatPendidikan?->siswa?->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            padding: 20px 30px;
        }

        /* ── KOP SURAT ── */
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
            height: 70px;
        }

        .kop-teks {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding: 0 10px;
        }

        .kop-teks .nama-institusi {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .kop-teks .nama-prodi {
            font-size: 11pt;
            font-weight: bold;
        }

        .kop-teks .alamat {
            font-size: 9pt;
            margin-top: 2px;
        }

        /* ── JUDUL DOKUMEN ── */
        .judul-dok {
            text-align: center;
            margin: 12px 0 10px;
        }

        .judul-dok h2 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: underline;
        }

        /* ── INFO MAHASISWA ── */
        .info-tabel {
            width: 100%;
            margin-bottom: 14px;
            font-size: 10.5pt;
        }

        .info-tabel td {
            padding: 2px 4px;
        }

        /* ── TABEL NILAI ── */
        .tabel-mk {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 10pt;
        }

        .tabel-mk th {
            border: 1px solid #000;
            background-color: #f0f0f0;
            padding: 5px 6px;
            text-align: center;
            font-weight: bold;
        }

        .tabel-mk td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .center {
            text-align: center;
        }

        /* ── SUMMARY ── */
        .summary-box {
            width: 100%;
            margin-top: 10px;
            border: 1px solid #000;
            padding: 10px;
            font-size: 10pt;
        }

        .summary-box table {
            width: 100%;
        }

        /* ── TANDA TANGAN ── */
        .ttd-area {
            width: 100%;
            margin-top: 30px;
            display: table;
        }

        .ttd-col {
            display: table-cell;
            width: 50%;
            text-align: center;
        }

        .ttd-col .ttd-label {
            font-size: 11pt;
            margin-bottom: 60px;
        }

        .ttd-col .ttd-garis {
            border-top: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            padding-top: 4px;
            font-size: 11pt;
            font-weight: bold;
        }

        .footer-doc {
            margin-top: 20px;
            font-size: 8pt;
            color: #555;
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="kop">
        <div class="kop-logo">
            <img src="{{ public_path('logokampus.jpg') }}" alt="Logo">
        </div>
        <div class="kop-teks">
            <div class="nama-institusi">{{ config('app.name', 'SIAKAD') }}</div>
            <div class="nama-prodi">{{ $krs->riwayatPendidikan?->jurusan?->nama }}</div>
            <div class="alamat">Alamat Institusi Terupdate | Telp: 08XXXXXXXX | Email: admin@siakad.com</div>
        </div>
    </div>

    <div class="judul-dok">
        <h2>KARTU HASIL STUDI (KHS)</h2>
    </div>

    @php
    $mhs = $krs->riwayatPendidikan?->siswa;
    $riwayat = $krs->riwayatPendidikan;
    @endphp

    <table class="info-tabel">
        <tr>
            <td width="120">NIM / Nama</td>
            <td width="10">:</td>
            <td>{{ $riwayat?->nomor_induk }} / {{ $mhs?->nama }}</td>
            <td width="120">Tahun Akademik</td>
            <td width="10">:</td>
            <td>{{ $krs->tahunAkademik?->nama ?? $krs->kode_tahun }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td>{{ $riwayat?->jurusan?->nama }}</td>
            <td>Semester</td>
            <td>:</td>
            <td>{{ $krs->semester }}</td>
        </tr>
    </table>

    <table class="tabel-mk">
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Kode</th>
                <th>Mata Kuliah</th>
                <th width="40">SKS</th>
                <th width="40">Nilai</th>
                <th width="40">Bobot</th>
                <th width="60">SKS x Bobot</th>
            </tr>
        </thead>
        <tbody>
            @php $totalSksRow = 0; $totalSkxBobot = 0; @endphp
            @foreach($krs->siswaDataLjk as $i => $ljk)
            @php
            $sks = $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->bobot ?? 0;
            $huruf = $ljk->Nilai_Huruf ?? '-';

            $bobot = $ljk->bobot;
            $skxBobot = $sks * $bobot;

            $totalSksRow += $sks;
            $totalSkxBobot += $skxBobot;
            @endphp
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td class="center">{{ $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->kode_feeder }}</td>
                <td>{{ $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->nama }}</td>
                <td class="center">{{ $sks }}</td>
                <td class="center">{{ $huruf }}</td>
                <td class="center">{{ number_format($bobot, 1) }}</td>
                <td class="center">{{ number_format($skxBobot, 1) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td colspan="3" style="text-align: right; padding-right: 10px;">TOTAL</td>
                <td class="center">{{ $totalSksRow }}</td>
                <td colspan="2"></td>
                <td class="center">{{ number_format($totalSkxBobot, 1) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="summary-box">
        <table>
            <tr>
                <td width="200">Total SKS Diambil</td>
                <td width="10">:</td>
                <td>{{ $totalSksRow }} SKS</td>
            </tr>
            <tr>
                <td>Indeks Prestasi Semester (IPS)</td>
                <td>:</td>
                <td><strong>{{ $ips }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="ttd-area">
        <div class="ttd-col">
            <div class="ttd-label">
                Mengetahui,<br>
                Ketua Program Studi
                <br><br><br><br>
                <span class="ttd-garis">{{ $kaprodi?->nama ?? '____________________' }}</span><br>
                NIDN. {{ $kaprodi?->NIPDN ?? '____________________' }}
            </div>
        </div>
        <div class="ttd-col">
            <div class="ttd-label">
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Mahasiswa,
                <br><br><br><br>
                <span class="ttd-garis">{{ $mhs?->nama }}</span><br>
                NIM. {{ $riwayat?->nomor_induk }}
            </div>
        </div>
    </div>

    <div class="footer-doc">
        Dicetak melalui Sistem Informasi Akademik pada {{ date('d/m/Y H:i') }}
    </div>

</body>

</html>