<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Nilai LJK</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            color: #000;
            background-color: #fff;
            margin: 0;
            /* padding: 20px; */
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

        .kop p {
            font-size: 12px;
            margin: 0;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
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
    </style>
</head>

<body>
    <div class="kop">
        <h1>REKAPITULASI NILAI MAHASISWA</h1>
        <p>Aplikasi SIAKAD Terpadu - {{ now()->format('d M Y H:i') }}</p>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px">NO</th>
                <th class="nowrap" style="width: 80px">NIM</th>
                <th class="text-left" style="width: 150px">NAMA PESERTA</th>
                <th class="text-left">MATA KULIAH</th>
                <th class="text-left">DOSEN</th>
                <th style="width: 40px">UTS</th>
                <th style="width: 40px">UAS</th>
                <th style="width: 40px">TGS 1</th>
                <th style="width: 40px">TGS 2</th>
                <th style="width: 40px">TGS 3</th>
                <th style="width: 40px">PERF</th>
                <th style="width: 50px">AKHIR</th>
                <th style="width: 40px">GRADE</th>
                <th style="width: 60px">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $idx => $record)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td class="nowrap">{{ $record->akademikKrs->riwayatPendidikan->nomor_induk ?? '-' }}</td>
                <td class="text-left">{{ $record->akademikKrs->riwayatPendidikan->siswa->nama ?? '-' }}</td>
                <td class="text-left">{{ $record->mataPelajaranKelas->mataPelajaranKurikulum->mataPelajaranMaster->nama ?? '-' }}</td>
                <td class="text-left">{{ $record->mataPelajaranKelas->dosenData->nama ?? '-' }}</td>
                <td>{{ $record->Nilai_UTS ?? '-' }}</td>
                <td>{{ $record->Nilai_UAS ?? '-' }}</td>
                <td>{{ $record->Nilai_TGS ?? '-' }}</td>
                <td>{{ $record->Nilai_TGS_2 ?? '-' }}</td>
                <td>{{ $record->Nilai_TGS_3 ?? '-' }}</td>
                <td>{{ $record->Nilai_Performance ?? '-' }}</td>
                <td style="font-weight: bold;">{{ $record->Nilai_Akhir ?? '-' }}</td>
                <td>{{ $record->Nilai_Huruf ?? '-' }}</td>
                <td>{{ $record->Status_Nilai ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="14" style="padding: 20px;">Tidak ada data nilai / filter kosong.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>