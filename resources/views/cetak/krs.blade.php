<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kartu Rencana Studi - {{ $krs->riwayatPendidikan?->siswa?->nama }}</title>
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

        .judul-dok p {
            font-size: 10pt;
            margin-top: 2px;
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

        .info-tabel td:nth-child(2) {
            width: 8px;
            text-align: center;
        }

        /* ── TABEL MATAKULIAH ── */
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
            vertical-align: top;
        }

        .tabel-mk td.center {
            text-align: center;
        }

        .tabel-mk tfoot td {
            font-weight: bold;
            border: 1px solid #000;
            padding: 4px 6px;
            background-color: #f5f5f5;
        }

        /* ── SYARAT & STATUS ── */
        .syarat-tabel {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 10pt;
        }

        .syarat-tabel th,
        .syarat-tabel td {
            border: 1px solid #000;
            padding: 4px 8px;
            text-align: center;
        }

        .syarat-tabel th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .badge-y {
            color: green;
            font-weight: bold;
        }

        .badge-n {
            color: #c00;
            font-weight: bold;
        }

        /* ── TANDA TANGAN ── */
        .ttd-area {
            width: 100%;
            margin-top: 20px;
            display: table;
        }

        .ttd-col {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 0 10px;
        }

        .ttd-col .ttd-label {
            font-size: 10pt;
            margin-bottom: 60px;
        }

        .ttd-col .ttd-garis {
            border-top: 1px solid #000;
            padding-top: 4px;
            font-size: 10pt;
        }

        /* ── FOOTER ── */
        .footer-doc {
            margin-top: 16px;
            font-size: 8.5pt;
            color: #555;
            text-align: right;
            border-top: 1px solid #ccc;
            padding-top: 4px;
        }
    </style>
</head>

<body>

    {{-- ══ KOP SURAT ══ --}}
    <div class="kop">
        <div class="kop-logo">
            <img src="{{ public_path('logokampus.jpg') }}" alt="Logo Kampus">
        </div>
        <div class="kop-teks">
            <div class="nama-institusi">{{ config('app.name', 'Nama Institusi') }}</div>
            <div class="nama-prodi">
                {{ $krs->riwayatPendidikan?->jurusan?->jenjangPendidikan?->nama ?? '' }}
                – {{ $krs->riwayatPendidikan?->jurusan?->nama ?? 'Program Studi' }}
            </div>
            <div class="alamat">Alamat Institusi &nbsp;|&nbsp; Telepon &nbsp;|&nbsp; Email</div>
        </div>
    </div>

    {{-- ══ JUDUL DOKUMEN ══ --}}
    <div class="judul-dok">
        <h2>Kartu Rencana Studi (KRS)</h2>
        <p>Semester {{ $krs->semester }} &mdash; Tahun Akademik {{ $krs->kelas?->tahunAkademik?->nama ?? $krs->kode_tahun ?? '-' }}</p>
    </div>

    {{-- ══ INFORMASI MAHASISWA ══ --}}
    @php
    $mahasiswa = $krs->riwayatPendidikan?->siswa;
    $riwayat = $krs->riwayatPendidikan;
    $jurusan = $riwayat?->jurusan;
    $kelas = $krs->kelas;
    @endphp

    <table class="info-tabel">
        <tr>
            <td width="130">Nama Mahasiswa</td>
            <td>:</td>
            <td><strong>{{ $mahasiswa?->nama ?? '-' }}</strong></td>
            <td width="130">Program Studi</td>
            <td>:</td>
            <td>{{ $jurusan?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>:</td>
            <td>{{ $riwayat?->nomor_induk ?? $mahasiswa?->nomor_induk ?? '-' }}</td>
            <td>Kelas</td>
            <td>:</td>
            <td>{{ $kelas?->programKelas?->nilai ?? '-' }}</td>
        </tr>
        <tr>
            <td>Angkatan</td>
            <td>:</td>
            <td>{{ $riwayat?->angkatan ?? '-' }}</td>
            <td>Jumlah SKS</td>
            <td>:</td>
            <td><strong>{{ $krs->jumlah_sks ?? '-' }} SKS</strong></td>
        </tr>
        <tr>
            <td>Tanggal KRS</td>
            <td>:</td>
            <td>{{ $krs->tgl_krs ? \Carbon\Carbon::parse($krs->tgl_krs)->translatedFormat('d F Y') : '-' }}</td>
            <td>Status Aktif</td>
            <td>:</td>
            <td>{{ $krs->status_aktif === 'Y' ? 'Aktif' : 'Tidak Aktif' }}</td>
        </tr>
    </table>

    {{-- ══ TABEL MATA KULIAH ══ --}}
    @php
    $mataPelajaranList = \App\Models\MataPelajaranKelas::with([
    'mataPelajaranKurikulum.mataPelajaranMaster',
    'dosenData',
    ])
    ->where('id_kelas', $krs->id_kelas)
    ->get();

    $totalSks = 0;
    @endphp

    <table class="tabel-mk">
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Kode</th>
                <th>Nama Mata Kuliah</th>
                <th width="45">SKS</th>
                <th>Dosen Pengampu</th>
                <th width="60">Hari/Jam</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mataPelajaranList as $i => $mk)
            @php
            $master = $mk->mataPelajaranKurikulum?->mataPelajaranMaster;
            $sks = $mk->mataPelajaranKurikulum?->sks ?? 0;
            $totalSks += $sks;
            @endphp
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td class="center">{{ $master?->kode ?? $master?->kode_feeder ?? '-' }}</td>
                <td>{{ $master?->nama ?? '-' }}</td>
                <td class="center">{{ $sks }}</td>
                <td>{{ $mk->dosenData?->nama ?? '-' }}</td>
                <td class="center">{{ $mk->hari ? Str::ucfirst(strtolower($mk->hari)) : '-' }}{{ $mk->jam ? ' / '.$mk->jam : '' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;color:#777;font-style:italic;">Tidak ada data mata kuliah</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right;">Total SKS</td>
                <td class="center">{{ $totalSks }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    {{-- ══ STATUS SYARAT ══ --}}
    <table class="syarat-tabel">
        <thead>
            <tr>
                <th>Syarat KRS</th>
                <th>Syarat UTS</th>
                <th>Syarat UAS</th>
                <th>Status Bayar</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <span class="{{ $krs->syarat_krs === 'Y' ? 'badge-y' : 'badge-n' }}">
                        {{ $krs->syarat_krs === 'Y' ? '✓ Terpenuhi' : '✗ Belum' }}
                    </span>
                </td>
                <td>
                    <span class="{{ $krs->syarat_uts === 'Y' ? 'badge-y' : 'badge-n' }}">
                        {{ $krs->syarat_uts === 'Y' ? '✓ Terpenuhi' : '✗ Belum' }}
                    </span>
                </td>
                <td>
                    <span class="{{ $krs->syarat_uas === 'Y' ? 'badge-y' : 'badge-n' }}">
                        {{ $krs->syarat_uas === 'Y' ? '✓ Terpenuhi' : '✗ Belum' }}
                    </span>
                </td>
                <td>
                    <span class="{{ $krs->status_bayar === 'Y' ? 'badge-y' : 'badge-n' }}">
                        {{ $krs->status_bayar === 'Y' ? '✓ Lunas' : '✗ Belum Lunas' }}
                    </span>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ══ TANDA TANGAN ══ --}}
    <div class="ttd-area">
        <div class="ttd-col">
            <div class="ttd-label">Mengetahui,<br>Dosen Wali / PA</div>
            <div class="ttd-garis">( _____________________ )</div>
        </div>
        <div class="ttd-col">
            <div class="ttd-label">Menyetujui,<br>Ketua Program Studi</div>
            <div class="ttd-garis">( _____________________ )</div>
        </div>
        <div class="ttd-col">
            <div class="ttd-label">
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }},<br>
                Mahasiswa Yang Bersangkutan
            </div>
            <div class="ttd-garis">( {{ $mahasiswa?->nama ?? '___________________' }} )</div>
        </div>
    </div>

    {{-- ══ FOOTER ══ --}}
    <div class="footer-doc">
        Dicetak oleh sistem pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}
        &nbsp;|&nbsp; Dokumen ini sah tanpa tanda tangan basah jika dicetak dari sistem resmi.
    </div>

</body>

</html>