<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>KTM - {{ $siswa->nama }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .no-print {
            margin-bottom: 30px;
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            max-width: 500px;
        }

        .ktm-container {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .ktm-card {
            width: 85.6mm;
            height: 53.98mm;
            background: white;
            border-radius: 3.5mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
            border: 0.5px solid #e2e8f0;
        }

        .front-card {
            background: linear-gradient(145deg, #0f172a 0%, #1e3a8a 100%);
            color: white;
        }

        .card-header {
            height: 14mm;
            background: white;
            display: flex;
            align-items: center;
            padding: 0 12px;
            border-bottom: 2.5px solid #fbbf24;
        }

        .card-header img {
            height: 32px;
            margin-right: 10px;
        }

        .header-title {
            color: #1e3a8a;
        }

        .header-title h1 {
            margin: 0;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .header-title p {
            margin: 0;
            font-size: 7px;
            font-weight: 600;
        }

        .card-body {
            display: flex;
            padding: 10px;
            gap: 12px;
        }

        .photo-box {
            width: 18mm;
            height: 24mm;
            background: #f8fafc;
            border: 1.5px solid #fbbf24;
            border-radius: 2px;
            overflow: hidden;
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .student-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            font-size: 6px;
            color: #fbbf24;
            font-weight: 700;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .barcode-area {
            position: absolute;
            bottom: 7px;
            right: 12px;
            text-align: right;
            background: white;
            padding: 3px 6px;
            border-radius: 4px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .barcode-img {
            height: 24px;
            width: 32mm;
            display: block;
        }

        .barcode-text {
            font-size: 8px;
            color: #1e3a8a;
            font-weight: 800;
            letter-spacing: 1.5px;
            margin-top: 1px;
            text-align: center;
        }

        .footer-text {
            position: absolute;
            bottom: 8px;
            left: 12px;
            font-size: 6px;
            opacity: 0.7;
            font-style: italic;
        }

        /* BACK CARD */
        .back-content {
            padding: 12px;
            height: 100%;
            display: flex;
            flex-direction: column;
            color: #1e3a8a;
        }

        .rules-title {
            font-size: 9px;
            font-weight: 800;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .rules-list {
            font-size: 7px;
            line-height: 1.4;
            padding-left: 12px;
            margin: 0;
        }

        .qr-section {
            margin-top: auto;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            padding: 6px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .qr-box {
            width: 14mm;
            height: 14mm;
            background: white;
            padding: 2px;
        }

        .qr-box img {
            width: 100%;
            height: 100%;
        }

        .qr-info {
            font-size: 7px;
        }

        .qr-info strong {
            display: block;
            font-size: 8px;
            color: #1e3a8a;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            .no-print {
                display: none;
            }

            .ktm-card {
                box-shadow: none;
                border: 0.1px solid #ccc;
                margin-bottom: 10mm;
            }

            @page {
                margin: 10mm;
            }
        }

        .btn {
            padding: 12px 24px;
            background: #1e3a8a;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button class="btn" onclick="window.print()">CETAK KARTU</button>
    </div>

    <div class="ktm-container">
        <!-- FRONT -->
        <div class="ktm-card front-card">
            <div class="card-header">
                <img src="{{ asset('logokampus.jpg') }}" alt="Logo" onerror="this.src='https://ui-avatars.com/api/?name=IPB&background=1e3a8a&color=fff'">
                <div class="header-title">
                    <h1>Kartu Tanda Mahasiswa</h1>
                    <p>Institut Pendidikan dan Bahasa</p>
                </div>
            </div>
            <div class="card-body">
                <div class="photo-box">
                    @if($siswa->foto_profil)
                    <img src="{{ Storage::url($siswa->foto_profil) }}" alt="Foto">
                    @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($siswa->nama) }}&background=f1f5f9&color=1e3a8a&size=200" alt="Foto">
                    @endif
                </div>
                <div class="student-info">
                    <div class="info-item">
                        <div class="info-label">Nama</div>
                        <div class="info-value">{{ Str::limit($siswa->nama, 22) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">NIM</div>
                        <div class="info-value" style="color:#fbbf24">{{ $siswa->riwayatPendidikanAktif->nomor_induk ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Program Studi</div>
                        <div class="info-value" style="font-size: 7px;">{{ $siswa->riwayatPendidikanAktif->jurusan->nama ?? '-' }}</div>
                    </div>
                </div>
            </div>
            <div class="footer-text">Berlaku Selama Menjadi Mahasiswa Aktif</div>
            <div class="barcode-area">
                @php $nim = $siswa->riwayatPendidikanAktif->nomor_induk ?? '000000'; @endphp
                <img src="https://quickchart.io/barcode?type=code128&text={{ $nim }}&width=200&height=40&includeText=false" class="barcode-img">
                <div class="barcode-text">{{ $nim }}</div>
            </div>
        </div>

        <!-- BACK -->
        <div class="ktm-card">
            <div class="back-content">
                <div class="rules-title">Ketentuan Penggunaan</div>
                <ol class="rules-list">
                    <li>Kartu ini adalah kartu identitas resmi mahasiswa.</li>
                    <li>Wajib dibawa saat kegiatan akademik di kampus.</li>
                    <li>Jika hilang, segera lapor ke bagian administrasi.</li>
                    <li>QR Code di bawah digunakan untuk presensi perpustakaan.</li>
                </ol>

                <div class="qr-section" style="justify-content: center; background: white; border: 1.5px dashed #1e3a8a;">
                    <div class="qr-box" style="width: 25mm; height: 25mm;">
                        @php $checkinUrl = route('library.checkin', ['nim' => $nim]); @endphp
                        <img src="https://quickchart.io/qr?text={{ urlencode($checkinUrl) }}&size=150" alt="QR Check-in">
                    </div>
                </div>

                <div style="margin-top:auto; font-size: 6px; color:#94a3b8; display:flex; justify-content: space-between;">
                    <span>siakad.ac.id</span>
                    <span>Dicetak: {{ date('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>