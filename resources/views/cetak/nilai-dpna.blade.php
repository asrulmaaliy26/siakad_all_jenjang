<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DPNA - {{ $kelas->mataPelajaranKurikulum->mataPelajaranMaster->nama ?? 'Mata Pelajaran' }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10px;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 10px;
        }

        .kop {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }

        .kop h1 {
            font-size: 16px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }

        .kop h2 {
            font-size: 13px;
            margin: 0 0 5px 0;
            font-weight: bold;
        }

        .info-kelas {
            width: 100%;
            margin-bottom: 15px;
            font-size: 11px;
            border-collapse: collapse;
        }

        .info-kelas td {
            padding: 2px 5px;
            vertical-align: top;
        }

        .info-kelas td.label {
            width: 120px;
            font-weight: bold;
        }

        .info-kelas td.separator {
            width: 10px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }

        table.data th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-transform: uppercase;
        }

        .text-left {
            text-align: left !important;
        }

        .nowrap {
            white-space: nowrap;
        }

        .ttd-box {
            width: 100%;
            margin-top: 20px;
            font-size: 11px;
        }

        .ttd-box td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .nama-ttd {
            font-weight: bold;
            text-decoration: underline;
            display: block;
            margin-top: 50px;
        }

        @media print {
            body {
                padding: 0;
            }

            @page {
                size: A4 portrait;
                margin: 0.5cm;
            }
        }
    </style>
</head>

<body>
    <div class="kop" style="margin-top: 5px;">
        <h1>DAFTAR PESERTA DAN NILAI AKHIR (DPNA)</h1>
        <h2>TAHUN AKADEMIK: {{ $kelas->kelas->tahunAkademik->nama ?? '-' }}</h2>
    </div>

    <table class="info-kelas" style="font-size: 10px;">
        <tr>
            <td class="label" style="width: 100px;">Fakultas / Prodi</td>
            <td class="separator">:</td>
            <td>{{ $kelas->kelas->jurusan->fakultas->nama ?? '-' }} / {{ $kelas->kelas->jurusan->nama ?? '-' }}</td>
            <td class="label" style="width: 100px;">Dosen Pengampu</td>
            <td class="separator">:</td>
            <td>{{ $kelas->dosenData->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Mata Kuliah</td>
            <td class="separator">:</td>
            <td>{{ $kelas->mataPelajaranKurikulum->mataPelajaranMaster->nama ?? '-' }} ({{ $kelas->mataPelajaranKurikulum->mataPelajaranMaster->kode_feeder ?? '-' }})</td>
            <td class="label">NIDN / NIDK</td>
            <td class="separator">:</td>
            <td>{{ $kelas->dosenData->nidn ?? $kelas->dosenData->nidk ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">SKS / Semester</td>
            <td class="separator">:</td>
            <td>{{ $kelas->mataPelajaranKurikulum->mataPelajaranMaster->bobot ?? '-' }} SKS / Semester {{ $kelas->mataPelajaranKurikulum->semester ?? '-' }}</td>
            <td class="label">Kelas / Program</td>
            <td class="separator">:</td>
            <td>{{ $kelas->kelas->id ?? '-' }} / {{ $kelas->kelas->programKelas->nilai ?? '-' }}</td>
        </tr>
    </table>

    <table class="data" style="font-size: 8px;">
        <thead>
            <tr>
                <th rowspan="2" style="width: 20px">NO</th>
                <th rowspan="2" style="width: 60px">NIM</th>
                <th rowspan="2" class="text-left">NAMA MAHASISWA</th>
                <th colspan="12" style="font-size: 7px;">TUGAS (1-12)</th>
                <th rowspan="2" style="width: 25px">PRF</th>
                <th rowspan="2" style="width: 25px">UTS</th>
                <th rowspan="2" style="width: 25px">UAS</th>
                <th rowspan="2" style="width: 30px">FIN</th>
                <th rowspan="2" style="width: 20px">G</th>
            </tr>
            <tr>
                @for($i = 1; $i <= 12; $i++)
                    <th style="width: 15px; font-size: 7px;">{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse($records as $idx => $record)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td class="nowrap">{{ $record->akademikKrs->riwayatPendidikan->nomor_induk ?? '-' }}</td>
                <td class="text-left">{{ $record->akademikKrs->riwayatPendidikan->siswa->nama ?? '-' }}</td>
                @for($i = 1; $i <= 12; $i++)
                    @php $field = "Nilai_TGS_" . ($i == 1 ? '' : $i); @endphp
                    <td>{{ $record->$field ?? '' }}</td>
                @endfor
                <td>{{ $record->Nilai_Performance ?? '' }}</td>
                <td>{{ $record->Nilai_UTS ?? '' }}</td>
                <td>{{ $record->Nilai_UAS ?? '' }}</td>
                <td style="font-weight: bold;">{{ $record->Nilai_Akhir ?? '' }}</td>
                <td style="font-weight: bold;">{{ $record->Nilai_Huruf ?? '' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="20" style="padding: 20px;">Belum ada data nilai mahasiswa untuk kelas ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="ttd-box">
        <tr>
            <td></td>
            <td>
                {{ config('app.location', 'Diterbitkan pada') }}, {{ now()->translatedFormat('d F Y') }}<br>
                Dosen Pengampu,<br><br><br><br>
                <span class="nama-ttd">{{ $kelas->dosenData->nama ?? '_________________________' }}</span>
                NIDN/NIDK: {{ $kelas->dosenData->nidn ?? $kelas->dosenData->nidk ?? '-' }}
            </td>
        </tr>
    </table>
</body>

</html>
