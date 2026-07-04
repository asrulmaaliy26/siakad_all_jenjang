<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ujian Diblokir</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 20px;
            padding: 60px 48px;
            max-width: 520px;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0,0,0,.4);
        }
        .icon { font-size: 72px; margin-bottom: 24px; }
        h1 { font-size: 24px; color: #fca5a5; margin-bottom: 16px; }
        p { color: #94a3b8; font-size: 14px; line-height: 1.8; }
        .badge {
            display: inline-block;
            background: #7f1d1d;
            border: 1px solid #ef4444;
            color: #fca5a5;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 18px;
            border-radius: 20px;
            margin: 20px 0;
        }
        .info-box {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 20px;
            margin-top: 24px;
            text-align: left;
        }
        .info-box .label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: .7px; margin-bottom: 4px; }
        .info-box .value { font-size: 14px; color: #e2e8f0; font-weight: 600; margin-bottom: 14px; }
        .info-box .value:last-child { margin-bottom: 0; }
        .back-btn {
            display: inline-flex; align-items: center; gap: 8px;
            margin-top: 32px;
            background: #334155; color: #94a3b8;
            border: 1px solid #475569; border-radius: 10px;
            padding: 12px 28px; font-size: 14px; text-decoration: none;
            transition: all .2s;
        }
        .back-btn:hover { background: #475569; color: #e2e8f0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🔒</div>
        <h1>Akses Ujian Anda Diblokir</h1>
        <div class="badge">5 Pelanggaran Terdeteksi</div>
        <p>Akses ujian Anda telah diblokir secara otomatis karena sistem mendeteksi
            5 pelanggaran selama sesi ujian berlangsung.</p>

        <div class="info-box">
            <div class="label">Mata Pelajaran</div>
            <div class="value">{{ $mpk->mataPelajaranKurikulum?->mataPelajaranMaster?->nama ?? '-' }}</div>

            <div class="label">Jenis Ujian</div>
            <div class="value">{{ strtoupper($type) }}</div>

            <div class="label">Jumlah Pelanggaran</div>
            <div class="value" style="color:#ef4444">
                {{ $ljk->{$type === 'uas' ? 'jml_pelanggaran_uas' : 'jml_pelanggaran_uts'} ?? 5 }}x pelanggaran
            </div>
        </div>

        <p style="margin-top:20px;">
            Hubungi dosen / pengajar mata pelajaran ini untuk mereset akses ujian Anda.
        </p>

        <a href="{{ url('/pekan-ujians/1') }}" class="back-btn">← Kembali ke Dashboard</a>
    </div>
</body>
</html>
