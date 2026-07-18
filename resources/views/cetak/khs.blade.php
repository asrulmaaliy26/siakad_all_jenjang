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
            width: 100%;
            margin-bottom: 15px;
            text-align: center;
        }

        .kop img {
            width: 100%;
            height: auto;
        }

        /* ÔöÇÔöÇ JUDUL DOKUMEN ÔöÇÔöÇ */
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

        /* ÔöÇÔöÇ INFO MAHASISWA ÔöÇÔöÇ */
        .info-tabel {
            width: 100%;
            margin-bottom: 14px;
            font-size: 10.5pt;
        }

        .info-tabel td {
            padding: 2px 4px;
        }

        /* ÔöÇÔöÇ TABEL NILAI ÔöÇÔöÇ */
        .tabel-mk {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 8.5pt;
        }

        .tabel-mk th {
            border: 1px solid #000;
            background-color: #f0f0f0;
            padding: 5px 2px;
            text-align: center;
            font-weight: bold;
        }

        .tabel-mk td {
            border: 1px solid #000;
            padding: 4px 2px;
            vertical-align: middle;
        }

        .center {
            text-align: center;
        }

        /* ÔöÇÔöÇ SUMMARY ÔöÇÔöÇ */
        .summary-box {
            width: 100%;
            margin-top: 10px;
            font-size: 10pt;
        }

        .summary-box table {
            width: 100%;
        }

        /* ÔöÇÔöÇ TANDA TANGAN ÔöÇÔöÇ */
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
        <img src="{{ public_path('assets/kopstaiman.jpeg') }}" alt="Kop STAI Al Mannan" style="width: 100%; max-height: 140px; object-fit: contain;">
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
            <td>{{ $riwayat ? $riwayat->getSemester(null, $krs->id_tahun_akademik) : '-' }}</td>
        </tr>
    </table>

    <table class="tabel-mk">
        <thead>
            <tr>
                <th rowspan="2" width="25">NO</th>
                <th rowspan="2" width="55">KODE</th>
                <th rowspan="2">MATA KULIAH</th>
                <th colspan="8">PRESTASI</th>
                <th rowspan="2" width="60">Predikat</th>
                <th rowspan="2" width="70">Rekomendasi</th>
            </tr>
            <tr>
                <th width="25">SKS</th>
                <th width="30">UTS</th>
                <th width="30">TGS</th>
                <th width="30">UAS</th>
                <th width="30">P</th>
                <th width="30">AM</th>
                <th width="30">HM</th>
                <th width="30">M</th>
            </tr>
        </thead>
        <tbody>
            @php $totalSksRow = 0; $totalSkxBobot = 0; @endphp
            @foreach($krs->siswaDataLjk as $i => $ljk)
            @php
            $sks = $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->bobot ?? 0;
            
            // Komponen Nilai
            $uts = $ljk->Nilai_UTS ?? 0;
            $uas = $ljk->Nilai_UAS ?? 0;
            $p = $ljk->Nilai_Performance ?? 0;
            
            // Hitung rata-rata TGS
            $tugasFields = ['Nilai_TGS_1', 'Nilai_TGS_2', 'Nilai_TGS_3', 'Nilai_TGS_4', 'Nilai_TGS_5', 'Nilai_TGS_6', 'Nilai_TGS_7', 'Nilai_TGS_8', 'Nilai_TGS_9', 'Nilai_TGS_10', 'Nilai_TGS_11', 'Nilai_TGS_12'];
            $totalTugas = 0;
            $countTugas = 0;
            foreach ($tugasFields as $field) {
                $val = $ljk->{$field};
                if (!is_null($val) && (float)$val > 0) {
                    $totalTugas += (float) $val;
                    $countTugas++;
                }
            }
            $tgs = $countTugas > 0 ? ($totalTugas / $countTugas) : 0;

            $am = $ljk->bobot; // Angka Mutu
            $hm = $ljk->Nilai_Huruf ?? '-'; // Huruf Mutu
            $m = $sks * $am; // Mutu (SKS x Bobot)
            
            $predikat = $ljk->Status_Nilai ?? '-'; // LULUS / TIDAK LULUS
            $rekomendasi = ''; // Dikosongkan sesuai format lama

            $totalSksRow += $sks;
            $totalSkxBobot += $m;
            @endphp
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td class="center">{{ $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->kode_feeder }}</td>
                <td>{{ $ljk->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->nama }}</td>
                <td class="center">{{ $sks }}</td>
                <td class="center">{{ number_format($uts, 2) }}</td>
                <td class="center">{{ number_format($tgs, 2) }}</td>
                <td class="center">{{ number_format($uas, 2) }}</td>
                <td class="center">{{ number_format($p, 2) }}</td>
                <td class="center">{{ number_format($am, 2) }}</td>
                <td class="center">{{ $hm }}</td>
                <td class="center">{{ number_format($m, 2) }}</td>
                <td class="center">{{ $predikat }}</td>
                <td class="center">{{ $rekomendasi }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td colspan="3" style="text-align: right; padding-right: 10px;">TOTAL</td>
                <td class="center">{{ $totalSksRow }}</td>
                <td colspan="6"></td>
                <td class="center">{{ number_format($totalSkxBobot, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="summary-box">
        <table style="width: 100%; border: none; font-size: 10pt;">
            <tr>
                <td width="60%" style="vertical-align: top; padding-right: 15px;">
                    Ket.<br>
                    Nilai kurang dari 2.00 / C- dan lebih kecil dinyatakan Tidak Lulus.<br>
                    Salah satu komponen nilai kosong dinyatakan tidak lulus<br>
                    <table style="width: 100%; border: none; margin-top: 5px; font-size: 10pt;">
                        <tr><td width="40">K/SKS</td><td width="10">:</td><td>Sistem Kredit Semester</td></tr>
                        <tr><td>UTS</td><td>:</td><td>Ujian Tengah Semester</td></tr>
                        <tr><td>TGS</td><td>:</td><td>Tugas</td></tr>
                        <tr><td>UAS</td><td>:</td><td>Ujian Akhir Semester</td></tr>
                        <tr><td>P</td><td>:</td><td>Performance</td></tr>
                        <tr><td>HM</td><td>:</td><td>Huruf Mutu</td></tr>
                        <tr><td>AM</td><td>:</td><td>Angka Mutu</td></tr>
                        <tr><td>M</td><td>:</td><td>Mutu (sks x Angka Mutu)</td></tr>
                    </table>
                </td>
                <td width="40%" style="vertical-align: top;">
                    <table style="width: 100%; border: none; font-size: 10pt;">
                        <tr><td width="170">SKS Semester Ini</td><td width="10">:</td><td>{{ $totalSksRow }}</td></tr>
                        <tr><td>SKS yang telah diselesaikan</td><td>:</td><td>{{ $totalSksKumulatif }}</td></tr>
                        <tr><td>SKS maks. yang dapat diambil</td><td>:</td><td>24</td></tr>
                        <tr><td>Total Nilai Semester Ini</td><td>:</td><td>{{ number_format($totalBobot, 2) }}</td></tr>
                        <tr><td>IPK Semester Ini</td><td>:</td><td>{{ number_format($ips, 2) }}</td></tr>
                        <tr><td>IPK Terakhir</td><td>:</td><td>{{ number_format($ipk, 2) }}</td></tr>
                    </table>
                </td>
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
                {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}<br>
                Mahasiswa,
                <br><br><br><br>
                <span class="ttd-garis">{{ $mhs?->nama }}</span><br>
                NIM. {{ $riwayat?->nomor_induk }}
            </div>
        </div>
    </div>


</body>

</html>
