<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Absensi Kosong - {{ $kelas->mataPelajaranKurikulum->mataPelajaranMaster->nama ?? 'Mata Pelajaran' }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 20px;
        }

        .kop {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }

        .kop h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }

        .kop h2 {
            font-size: 14px;
            margin: 0 0 5px 0;
            font-weight: normal;
        }

        .kop p {
            font-size: 12px;
            margin: 0;
        }

        .info-kelas {
            width: 100%;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .info-kelas td {
            padding: 3px;
            vertical-align: top;
        }

        .info-kelas td:nth-child(1) {
            width: 120px;
            font-weight: bold;
        }

        .info-kelas td:nth-child(2) {
            width: 10px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }

        table.data th {
            background-color: #e5e7eb;
            font-weight: bold;
        }

        .text-left {
            text-align: left !important;
        }

        .nowrap {
            white-space: nowrap;
        }

        .ttd-box {
            width: 100%;
            margin-top: 30px;
        }

        .ttd-box td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            height: 100px;
        }

        .nama-ttd {
            font-weight: bold;
            text-decoration: underline;
        }

        @media print {
            body {
                padding: 0;
            }

            @page {
                size: A4 portrait;
                margin: 1cm;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <!-- Tombol Print (Sembunyi saat diprint) -->
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 15px; background: #2563eb; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">📄 Cetak PDF / Print</button>
    </div>

    <div class="kop">
        <h1>DAFTAR HADIR MAHASISWA</h1>
        <h2>TAHUN AKADEMIK: {{ $kelas->kelas->tahunAkademik->nama ?? '-' }} - PERIODE {{ $kelas->kelas->tahunAkademik->periode ?? '-' }}</h2>
    </div>

    <table class="info-kelas">
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td style="width: 40%">{{ $kelas->kelas->jurusan->nama ?? '-' }}</td>
            <td>Dosen Pengampu</td>
            <td>:</td>
            <td>{{ $kelas->dosenData->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Mata Kuliah / Kelas</td>
            <td>:</td>
            <td>{{ $kelas->mataPelajaranKurikulum->mataPelajaranMaster->nama ?? '-' }} / {{ $kelas->kelas->programKelas->nilai ?? '-' }}</td>
            <td>Jadwal / Ruang</td>
            <td>:</td>
            <td>{{ $kelas->hari ?? '-' }}, {{ $kelas->jam ?? '-' }} / {{ $kelas->ruangKelas?->nilai ?? '-' }}</td>
        </tr>
        <tr>
            <td>SKS / SMT</td>
            <td>:</td>
            <td colspan="4">{{ $kelas->mataPelajaranKurikulum->mataPelajaranMaster->bobot ?? '-' }} SKS / SMT {{ $kelas->mataPelajaranKurikulum->semester ?? '-' }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px">NO</th>
                <th rowspan="2" class="nowrap" style="width: 75px">NIM</th>
                <th rowspan="2" class="text-left">NAMA MAHASISWA</th>
                <th colspan="12">PERTEMUAN KE-</th>
                <th rowspan="2" style="width: 40px">KET</th>
            </tr>
            <tr>
                @for($i = 1; $i <= 12; $i++)
                    <th style="width: 20px; font-size: 9px;">{{ $i }}</th>
                    @endfor
            </tr>
        </thead>
        <tbody>
            @forelse($krsList as $idx => $krs)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td class="nowrap">{{ $krs->riwayatPendidikan->siswaData->nomor_induk ?? '-' }}</td>
                <td class="text-left">{{ $krs->riwayatPendidikan->siswaData->nama ?? '-' }}</td>
                @for($i = 1; $i <= 12; $i++)
                    <td>
                    </td>
                    @endfor
                    <td></td>
            </tr>
            @empty
            <tr>
                <td colspan="16" style="padding: 20px;">Belum ada mahasiswa yang mengambil kelas ini (KRS).</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="ttd-box">
        <tr>
            <td></td>
            <td>
                Mengetahui,<br>
                Dosen Pengampu<br><br><br><br><br>
                <span class="nama-ttd">{{ $kelas->dosenData->nama ?? '_________________________' }}</span><br>
                NIDN/NIDK: {{ $kelas->dosenData->nidn ?? $kelas->dosenData->nidk ?? '-' }}
            </td>
        </tr>
    </table>
</body>

</html>