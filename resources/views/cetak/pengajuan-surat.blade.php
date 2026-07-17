<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Pengajuan - {{ $pengajuan->riwayatPendidikan?->siswa?->nama_lengkap }}</title>
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
            padding: 40px 50px;
            line-height: 1.5;
        }

                /* -- KOP SURAT -- */
        .kop {
            width: 100%;
            margin-bottom: 15px;
            text-align: center;
        }

        .kop img {
            width: 100%;
            height: auto;
        }

        /* ── INFO SURAT ── */
        .tgl-surat {
            text-align: right;
            margin-bottom: 20px;
        }

        .nomor-surat {
            margin-bottom: 20px;
        }

        /* ── JUDUL DOKUMEN ── */
        .judul-dok {
            text-align: center;
            margin: 20px 0 30px;
        }

        .judul-dok h2 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
        }

        /* ── ISI SURAT ── */
        .isi-surat {
            text-align: justify;
            margin-bottom: 30px;
        }

        .info-mhs {
            margin: 15px 0 15px 30px;
            width: 100%;
        }

        .info-mhs td {
            padding: 2px 0;
            vertical-align: top;
        }

        /* ── TANDA TANGAN ── */
        .ttd-area {
            width: 100%;
            margin-top: 40px;
        }

        .ttd-table {
            width: 100%;
        }

        .ttd-col {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .ttd-label {
            margin-bottom: 60px;
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }

        /* ── FOOTER ── */
        .footer-doc {
            position: fixed;
            bottom: 20px;
            left: 50px;
            right: 50px;
            font-size: 8pt;
            color: #777;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    {{-- ══ KOP SURAT ══ --}}
        <div class="kop">
        <img src="{{ public_path('assets/kopstaiman.jpeg') }}" alt="Kop STAI Al Mannan" style="width: 100%; max-height: 140px; object-fit: contain;">
    </div>

    <div class="tgl-surat">
        {{ \Carbon\Carbon::parse($pengajuan->created_at)->translatedFormat('d F Y') }}
    </div>

    <div class="nomor-surat">
        Hal : <strong>Permohonan {{ \App\Models\PengajuanSurat::getJenisOptions()[$pengajuan->jenis_surat] ?? 'Surat' }}</strong>
    </div>

    <div class="isi-surat">
        <p>Yth. Bagian Akademik / Dekan Fakultas</p>
        <p>{{ config('app.name') }}</p>
        <p>Di Tempat</p>

        <br>

        <p>Dengan hormat,</p>
        <p>Saya yang bertanda tangan di bawah ini:</p>

        <table class="info-mhs">
            <tr>
                <td width="150">Nama Lengkap</td>
                <td width="10">:</td>
                <td><strong>{{ $pengajuan->riwayatPendidikan?->siswa?->nama_lengkap }}</strong></td>
            </tr>
            <tr>
                <td>NIM / No. Induk</td>
                <td>:</td>
                <td>{{ $pengajuan->riwayatPendidikan?->nomor_induk }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td>{{ $pengajuan->riwayatPendidikan?->jurusan?->nama }}</td>
            </tr>
            <tr>
                <td>Semester / Angkatan</td>
                <td>:</td>
                <td>{{ $pengajuan->riwayatPendidikan?->getSemester() }} / {{ $pengajuan->riwayatPendidikan?->angkatan }}</td>
            </tr>
        </table>

        <p>Dengan ini mengajukan permohonan <strong>{{ \App\Models\PengajuanSurat::getJenisOptions()[$pengajuan->jenis_surat] ?? 'Surat' }}</strong> untuk keperluan:</p>

        <div style="margin: 10px 0 20px 30px; font-style: italic; border-left: 3px solid #ddd; padding-left: 10px;">
            "{{ $pengajuan->keperluan }}"
        </div>

        <p>Demikian surat permohonan ini saya sampaikan. Atas perhatian dan kerjasamanya, saya ucapkan terima kasih.</p>
    </div>

    <div class="ttd-area">
        <table class="ttd-table">
            <tr>
                <td class="ttd-col">
                    <div class="ttd-label">
                        Mengetahui,<br>
                        Dosen Wali / PA
                    </div>
                    <div class="ttd-nama">
                        ( {{ $pengajuan->riwayatPendidikan?->waliDosen?->nama ?? '_____________________' }} )
                    </div>
                    <div>NIDN. {{ $pengajuan->riwayatPendidikan?->waliDosen?->NIPDN ?? '_____________________' }}</div>
                </td>
                <td class="ttd-col">
                    <div class="ttd-label">
                        Hormat Saya,<br>
                        Mahasiswa
                    </div>
                    <div class="ttd-nama">
                        ( {{ $pengajuan->riwayatPendidikan?->siswa?->nama_lengkap }} )
                    </div>
                    <div>NIM. {{ $pengajuan->riwayatPendidikan?->nomor_induk }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; padding-top: 40px;">
                    @php
                    $kaprodi = \App\Models\DosenData::where('id_jurusan', $pengajuan->riwayatPendidikan?->id_jurusan)
                    ->whereHas('user', function($q) {
                    $q->whereHas('roles', function($r) {
                    $r->where('name', 'kaprodi');
                    });
                    })->first();
                    @endphp
                    <div class="ttd-label">
                        Menyetujui,<br>
                        Ketua Program Studi
                    </div>
                    <div class="ttd-nama">
                        ( {{ $kaprodi?->nama ?? '_____________________' }} )
                    </div>
                    <div>NIDN. {{ $kaprodi?->NIPDN ?? '_____________________' }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>