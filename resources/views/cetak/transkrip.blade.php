<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Transkrip Nilai - {{ $siswa->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            color: #000;
            padding: 20px;
        }

        .judul-dok {
            text-align: center;
            margin: 15px 0 25px 0;
        }

        .judul-dok h3 {
            font-size: 14pt;
            font-weight: bold;
        }

        .table-mhs {
            width: 100%;
            margin-bottom: 15px;
            display: table;
        }
        
        .table-mhs-col {
            display: table-cell;
            width: 50%;
        }

        .table-mhs-inner {
            width: 100%;
            font-size: 11pt;
            line-height: 1.3;
        }

        .tabel-nilai {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            line-height: 1.5;
        }

        .tabel-nilai th, .tabel-nilai td {
            border: 1px solid #000;
            padding: 4px;
        }

        .text-center {
            text-align: center;
        }

        .row-tables {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .col-table {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 5px;
        }

        .summary-area {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .summary-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .summary-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 40px;
        }

        .summary-table {
            width: 100%;
            font-size: 11pt;
            line-height: 1.5;
        }

    </style>
</head>
<body>

    <div style="text-align: center; margin-bottom: 15px;">
        <img src="{{ public_path('assets/kopstaiman.jpeg') }}" alt="Kop STAI Al Mannan" style="width: 100%; max-height: 140px; object-fit: contain;">
    </div>

    <div class="judul-dok">
        <h3><b>TRANSKRIP HASIL STUDI</b></h3>
    </div>

    <div class="table-mhs">
        <div class="table-mhs-col">
            <table class="table-mhs-inner">
                <tr>
                    <td width="35%">Nama Mahasiswa</td>
                    <td width="65%">: <strong>{{ $siswa->nama }}</strong></td>
                </tr>
                <tr>
                    <td>NIM / NPM</td>
                    <td>: {{ $riwayat->nomor_induk ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Tahun Masuk</td>
                    <td>: {{ $riwayat->tahunAkademik->nama ?? '-' }}</td>
                </tr>
            </table>
        </div>
        <div class="table-mhs-col">
            <table class="table-mhs-inner">
                <tr>
                    <td width="35%">Fakultas</td>
                    <td width="65%">: {{ $riwayat->jurusan->fakultas->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Program Studi</td>
                    <td>: {{ $riwayat->jurusan->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Kelas Perkuliahan</td>
                    <td>: {{ $riwayat->programKelas->nilai ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row-tables">
        @php 
            $count = $allLjk->count();
            // We use max(1) to avoid division by zero if count is 0
            $chunkSize = ceil(max($count, 1) / 2);
            $chunked = $allLjk->chunk($chunkSize); 
            $no = 1;
        @endphp

        @forelse($chunked as $chunk)
            <div class="col-table">
                <table class="tabel-nilai">
                    <thead>
                        <tr>
                            <th width="10%">No</th>
                            <th width="60%">Mata Kuliah</th>
                            <th width="10%">SKS</th>
                            <th width="10%">NA</th>
                            <th width="10%">NH</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chunk as $ljk)
                        @php 
                            $sks = $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->bobot ?? 0;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->nama ?? '-' }}</td>
                            <td class="text-center">{{ $sks }}</td>
                            <td class="text-center">{{ number_format($ljk->bobot, 2) }}</td>
                            <td class="text-center">{{ $ljk->Nilai_Huruf ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="col-table" style="width: 100%;">
                <table class="tabel-nilai">
                    <tr><td class="text-center">Belum ada data nilai.</td></tr>
                </table>
            </div>
        @endforelse
    </div>

    <div class="summary-area">
        <div class="summary-left">
            <table class="summary-table">
                <tr>
                    <td width="55%">TOTAL SKS</td>
                    <td width="45%">: {{ $totalSks }}</td>
                </tr>
                <tr>
                    <td>INDEKS PRESTASI KUMULATIF</td>
                    <td>: {{ number_format($ipk, 2) }}</td>
                </tr>
                <tr>
                    <td>PREDIKAT KELULUSAN</td>
                    <td>: 
                        @php
                            if($ipk > 3.75) echo "Cumlaude";
                            elseif($ipk > 3.50 && $ipk <= 3.75) echo "Sangat Memuaskan";
                            elseif($ipk > 3.00 && $ipk <= 3.50) echo "Memuaskan";
                            else echo "-";
                        @endphp
                    </td>
                </tr>
            </table>
        </div>
        <div class="summary-right">
            Jombang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
            Kaprodi
            <br><br><br><br>
            <strong>{{ $kaprodi?->nama ?? '____________________' }}</strong><br>
            {{ $kaprodi?->NIPDN ?? 'NIDN. __________' }}
        </div>
    </div>

</body>
</html>
