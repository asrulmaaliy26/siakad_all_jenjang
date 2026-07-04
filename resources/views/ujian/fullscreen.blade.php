<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ujian {{ strtoupper($type) }} – {{ $mpk->mataPelajaranKurikulum?->mataPelajaranMaster?->nama }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            user-select: none;
        }

        /* ─── TOPBAR ─── */
        #topbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: #1e293b;
            border-bottom: 2px solid #334155;
            padding: 0 24px;
            height: 60px;
            display: flex; align-items: center; justify-content: space-between;
        }
        #topbar .info { display: flex; align-items: center; gap: 16px; }
        #topbar .badge {
            background: #3b82f6; color: #fff; font-size: 11px; font-weight: 700;
            padding: 3px 10px; border-radius: 20px; letter-spacing: .5px;
        }
        #topbar .matkul { font-weight: 600; font-size: 15px; color: #f1f5f9; }
        #topbar .meta { font-size: 12px; color: #94a3b8; }
        #topbar .right { display: flex; align-items: center; gap: 12px; }

        /* Violation badge */
        #violation-badge {
            display: flex; align-items: center; gap: 6px;
            background: #1e3a5f; border: 1px solid #2563eb;
            color: #93c5fd; font-size: 12px; font-weight: 600;
            padding: 4px 12px; border-radius: 20px; transition: all .3s;
        }
        #violation-badge.warning { background: #7c2d12; border-color: #f97316; color: #fdba74; }
        #violation-badge.danger  { background: #7f1d1d; border-color: #ef4444; color: #fca5a5; animation: pulse 1s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.6} }

        /* Fullscreen btn */
        #fullscreen-btn {
            background: #334155; border: 1px solid #475569; color: #94a3b8;
            padding: 6px 12px; border-radius: 8px; cursor: pointer; font-size: 12px;
            display: flex; align-items: center; gap: 6px; transition: all .2s;
        }
        #fullscreen-btn:hover { background: #475569; color: #e2e8f0; }



        /* ─── OVERLAY PELANGGARAN ─── */
        #violation-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 999;
            background: rgba(127,29,29,.96);
            flex-direction: column; align-items: center; justify-content: center;
            text-align: center; padding: 40px;
        }
        #violation-overlay.show { display: flex; }
        #violation-overlay h2 { font-size: 28px; color: #fca5a5; margin-bottom: 12px; }
        #violation-overlay p  { color: #fecaca; font-size: 15px; max-width: 500px; line-height: 1.7; }
        #violation-overlay .counter { font-size: 72px; font-weight: 900; color: #ef4444; margin: 20px 0; line-height: 1; }
        #violation-overlay .rule-info {
            background: rgba(0,0,0,.3); border: 1px solid #991b1b;
            border-radius: 10px; padding: 12px 20px; margin: 16px 0;
            font-size: 13px; color: #fca5a5; max-width: 440px;
        }

        /* ─── BLOCKED OVERLAY ─── */
        #blocked-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 1000;
            background: #0f172a;
            flex-direction: column; align-items: center; justify-content: center;
            text-align: center; padding: 40px;
        }
        #blocked-overlay.show { display: flex; }
        #blocked-overlay svg { width: 80px; height: 80px; color: #ef4444; margin-bottom: 24px; }
        #blocked-overlay h2 { font-size: 26px; color: #fca5a5; margin-bottom: 12px; }
        #blocked-overlay p  { color: #94a3b8; font-size: 14px; max-width: 440px; line-height: 1.7; }

        /* ─── MAIN LAYOUT ─── */
        #main { margin-top: 60px; display: flex; height: calc(100vh - 60px); }

        /* Panel kiri: soal */
        #soal-panel {
            width: 55%; 
            overflow: hidden; position: relative; background: #0f172a;
            display: flex; flex-direction: column;
        }
        #soal-panel .panel-header {
            background: #1e293b; padding: 12px 20px;
            font-size: 13px; font-weight: 600; color: #94a3b8;
            border-bottom: 1px solid #334155; flex-shrink: 0;
            display: flex; align-items: center; gap: 8px;
        }
        #soal-panel .panel-content { flex: 1; overflow: auto; }
        iframe#soal-frame { width: 100%; height: 100%; border: none; }

        #soal-note {
            background: #1e293b;
            padding: 24px 28px;
            line-height: 1.8;
            font-size: 14px;
            color: #e2e8f0;
            height: 100%;
            overflow: auto;
        }
        #soal-note h1,#soal-note h2,#soal-note h3 { color: #f1f5f9; margin-bottom: 12px; }
        #soal-note p { margin-bottom: 10px; }
        #soal-note ul,#soal-note ol { margin: 10px 0 10px 20px; }

        /* Panel kanan: jawaban */
        #jawaban-panel {
            flex: 1; display: flex; flex-direction: column;
            background: #111827; overflow: hidden;
        }
        #jawaban-panel .panel-header {
            background: #1e293b; padding: 12px 20px;
            font-size: 13px; font-weight: 600; color: #94a3b8;
            border-bottom: 1px solid #334155; flex-shrink: 0;
            display: flex; align-items: center; gap: 8px;
        }
        #jawaban-panel .panel-content { flex: 1; overflow: auto; padding: 20px; }

        /* Form elements */
        label.field-label {
            display: block; font-size: 12px; font-weight: 600;
            color: #64748b; text-transform: uppercase; letter-spacing: .7px;
            margin-bottom: 8px; margin-top: 20px;
        }
        label.field-label:first-child { margin-top: 0; }

        textarea#ctt-jawaban {
            width: 100%; min-height: 200px;
            background: #1e293b; border: 1px solid #334155;
            color: #e2e8f0; border-radius: 10px; padding: 14px;
            font-size: 14px; line-height: 1.6; resize: vertical;
            font-family: inherit; outline: none; transition: border .2s;
        }
        textarea#ctt-jawaban:focus { border-color: #3b82f6; }

        /* File upload zone */
        #upload-zone {
            border: 2px dashed #334155; border-radius: 10px;
            padding: 28px; text-align: center; cursor: pointer;
            transition: all .2s; background: #1e293b;
        }
        #upload-zone:hover, #upload-zone.dragover {
            border-color: #3b82f6; background: #1e3a5f;
        }
        #upload-zone p { color: #64748b; font-size: 13px; margin-top: 8px; }
        #upload-zone .icon { font-size: 32px; }
        #file-input { display: none; }

        #file-list { margin-top: 12px; display: flex; flex-direction: column; gap: 8px; }
        .file-item {
            display: flex; align-items: center; gap: 10px;
            background: #1e3a5f; border: 1px solid #2563eb;
            border-radius: 8px; padding: 8px 14px;
            font-size: 13px; color: #93c5fd;
        }
        .file-item button {
            margin-left: auto; background: none; border: none;
            color: #ef4444; cursor: pointer; font-size: 16px; line-height: 1;
        }

        /* Saved files */
        #saved-files { margin-top: 10px; display: flex; flex-direction: column; gap: 6px; }
        .saved-file-item {
            display: flex; align-items: center; gap: 8px;
            background: #064e3b; border: 1px solid #059669;
            border-radius: 8px; padding: 8px 14px;
            font-size: 12px; color: #6ee7b7;
            text-decoration: none;
        }
        .saved-file-item:hover { background: #065f46; }

        /* Submit button */
        #submit-btn {
            width: 100%; padding: 16px;
            background: linear-gradient(135deg, #059669, #0d9488);
            border: none; color: #fff; font-size: 16px; font-weight: 700;
            border-radius: 12px; cursor: pointer; transition: all .2s;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            margin-top: 28px; letter-spacing: .3px;
        }
        #submit-btn:hover { background: linear-gradient(135deg, #047857, #0f766e); transform: translateY(-1px); }
        #submit-btn:active { transform: none; }
        #submit-btn:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }

        /* No select */
        .no-select { user-select: none !important; }

        /* ─── INTRO SCREEN ─── */
        #intro-screen {
            position: fixed; inset: 0; z-index: 2000;
            background: #0f172a;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; text-align: center; padding: 40px;
        }
        #intro-screen.hide { display: none; }
        #intro-screen h2 { font-size: 32px; color: #f8fafc; margin-bottom: 16px; }
        #intro-screen p { color: #94a3b8; font-size: 16px; max-width: 600px; line-height: 1.6; margin-bottom: 32px; }
        #btn-mulai {
            background: #3b82f6; border: none; color: #fff;
            padding: 16px 40px; border-radius: 12px; font-size: 18px; font-weight: 700;
            cursor: pointer; transition: all .2s; box-shadow: 0 4px 15px rgba(59,130,246,.3);
        }
        #btn-mulai:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(59,130,246,.4); }

        /* Resizer */
        #resizer {
            width: 6px;
            background: #334155;
            cursor: col-resize;
            flex-shrink: 0;
            z-index: 10;
        }
        #resizer:hover, #resizer.dragging {
            background: #3b82f6;
        }

    <style>
        .btn-fmt { background:transparent; border:none; color:#94a3b8; cursor:pointer; font-size:14px; padding:4px 8px; border-radius:4px; transition:all 0.2s; font-family:inherit;}
        .btn-fmt:hover { background:#334155; color:#fff; }
    </style>
</head>
<body>

{{-- ═══════════════════ INTRO SCREEN ═══════════════════ --}}
<div id="intro-screen">
    <h2>Siap Memulai Ujian?</h2>
    <p>Ujian ini menggunakan sistem pengawasan layar penuh (fullscreen). Keluar dari mode layar penuh atau berpindah tab akan memicu peringatan dan dapat dicatat sebagai pelanggaran.</p>
    <button id="btn-mulai" onclick="startExam()">Mulai Ujian Sekarang</button>
</div>

{{-- ═══════════════════ TOPBAR ═══════════════════ --}}
<div id="topbar">
    <div class="info">
        <span class="badge">UJIAN {{ strtoupper($type) }}</span>
        <div>
            <div class="matkul">{{ $mpk->mataPelajaranKurikulum?->mataPelajaranMaster?->nama ?? 'Mata Pelajaran' }}</div>
            <div class="meta">{{ $mpk->dosenData?->nama ?? '-' }} &bull; Kelas {{ $mpk->kelas?->semester }}</div>
        </div>
    </div>
    <div class="right">
        <div id="violation-badge">
            ⚠️ <span id="vcount">{{ $jmlPelanggaran }}</span>/3 pelanggaran
        </div>
        <button id="fullscreen-btn" onclick="enterFullscreen()">⛶ Layar Penuh</button>
    </div>
</div>

{{-- ═══════════════════ VIOLATION OVERLAY ═══════════════════ --}}
<div id="violation-overlay">
    <div class="counter" id="overlay-count">0</div>
    <h2>🚨 Pelanggaran Resmi Tercatat!</h2>
    <p id="overlay-msg">Anda terdeteksi meninggalkan halaman ujian.</p>
    <div class="rule-info">
        📌 <strong>Aturan:</strong> Keluar dari layar penuh atau berpindah tab
        akan dihitung sebagai <strong>1 pelanggaran</strong>.<br>
        Setelah <strong>3 pelanggaran</strong>, akses ujian Anda dan Siakad akan diblokir.
    </div>
    <div style="margin-top: 24px; color: white; font-weight: bold; animation: pulse 1s infinite;">
        ⏳ Memulihkan layar penuh secara otomatis...
    </div>
</div>

{{-- ═══════════════════ BLOCKED OVERLAY ═══════════════════ --}}
<div id="blocked-overlay">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color:#ef4444">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
    </svg>
    <h2>Akses Ujian Diblokir</h2>
    <p>Anda telah melakukan <strong style="color:#fca5a5">3 pelanggaran</strong> selama ujian berlangsung.<br>
    Akses ujian Anda telah diblokir secara otomatis.<br><br>
    Hubungi pengajar / dosen untuk mereset status ujian Anda jika ingin melanjutkan.</p>
</div>

{{-- ═══════════════════ MAIN CONTENT ═══════════════════ --}}
<div id="main">

    {{-- Panel Soal --}}
    <div id="soal-panel">
        <div class="panel-header">
            📄 Soal {{ strtoupper($type) }}
        </div>
        <div class="panel-content">
            @if($fileUrl)
                @if(in_array($fileExt, ['jpg','jpeg','png','gif','webp']))
                    <div style="display:flex;align-items:center;justify-content:center;height:100%;padding:20px;background:#0f172a;">
                        <img src="{{ $fileUrl }}" alt="Soal" style="max-width:100%;max-height:100%;object-fit:contain;border-radius:8px;box-shadow:0 0 30px rgba(0,0,0,.5);">
                    </div>
                @elseif(in_array($fileExt, ['pdf']))
                    <iframe id="soal-frame" src="{{ $fileUrl }}#toolbar=0&navpanes=0&scrollbar=1" title="Soal Ujian"></iframe>
                @elseif(in_array($fileExt, ['doc','docx']))
                    <iframe id="soal-frame" src="https://docs.google.com/viewer?url={{ urlencode($fileUrl) }}&embedded=true" title="Soal Ujian"></iframe>
                @else
                    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:#64748b;gap:16px;">
                        <div style="font-size:48px;">📎</div>
                        <p>File tidak dapat dipreview</p>
                        <a href="{{ $fileUrl }}" target="_blank" style="color:#3b82f6;">Unduh File Soal</a>
                    </div>
                @endif
            @elseif($soalNote)
                <div id="soal-note">{!! $soalNote !!}</div>
            @else
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:#64748b;gap:12px;">
                    <div style="font-size:48px;">📭</div>
                    <p>Soal belum tersedia. Hubungi pengajar.</p>
                </div>
            @endif
        </div>

        {{-- Note tambahan di bawah file --}}
        @if($fileUrl && $soalNote)
        <div style="border-top:1px solid #1e293b;background:#1e293b;padding:16px 20px;max-height:180px;overflow:auto;flex-shrink:0;">
            <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">📌 Instruksi / Catatan</div>
            <div style="font-size:13px;color:#cbd5e1;line-height:1.7;">{!! $soalNote !!}</div>
        </div>
        @endif
    </div>

    {{-- Resizer --}}
    <div id="resizer"></div>

    {{-- Panel Jawaban --}}
    <div id="jawaban-panel">
        <div class="panel-header">
            ✏️ Input Jawaban {{ strtoupper($type) }}
        </div>
        <div class="panel-content">
            <form id="jawaban-form" action="{{ route('ujian.submit', [$mpk->id, $type]) }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- File jawaban tersimpan --}}
                @php
                    $ljkField = $type === 'uas' ? 'ljk_uas' : 'ljk_uts';
                    $cttField = $type === 'uas' ? 'ctt_uas' : 'ctt_uts';
                    $savedFiles = $ljk->$ljkField ?? [];
                    if (is_string($savedFiles)) $savedFiles = json_decode($savedFiles, true) ?? [];
                    $savedCtt = $ljk->$cttField ?? '';
                @endphp

                @if(!empty($savedFiles))
                <label class="field-label">📂 File Jawaban Tersimpan</label>
                <div id="saved-files">
                    @foreach($savedFiles as $f)
                    <a href="{{ asset('storage/'.$f) }}" target="_blank" class="saved-file-item">
                        📄 {{ basename($f) }} <span style="margin-left:auto;font-size:10px;opacity:.7">↗ Buka</span>
                    </a>
                    @endforeach
                </div>
                @endif

                <label class="field-label" style="margin-top:{{ !empty($savedFiles) ? '20px' : '0' }}; display: flex; align-items: center; justify-content: space-between;">
                    <span>📎 Upload File Jawaban (LJK {{ strtoupper($type) }})</span>
                    <button type="button" onclick="allowFocusLossForUSB()" style="background: #3b82f6; color: white; border: none; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-size: 11px; font-weight: bold;">🔌 Izinkan Buka Folder / USB</button>
                </label>
                <div id="upload-zone" onclick="document.getElementById('file-input').click()"
                     ondragover="event.preventDefault();this.classList.add('dragover')"
                     ondragleave="this.classList.remove('dragover')"
                     ondrop="handleDrop(event)">
                    <div class="icon">📤</div>
                    <p>Klik atau seret file ke sini</p>
                    <p style="font-size:11px;margin-top:4px;">PDF, Word, atau Gambar (maks. 10MB)</p>
                </div>
                <input type="file" id="file-input" name="{{ $ljkField }}[]" multiple
                       accept=".pdf,.doc,.docx,image/*"
                       onchange="handleFiles(this.files)">
                <div id="file-list"></div>

                <label class="field-label" style="margin-top:24px">✍️ Jawaban / Catatan Teks</label>
                
                {{-- Toolbar Custom Editor --}}
                <div id="custom-toolbar" style="background:#1e293b; padding:8px 12px; border:1px solid #334155; border-bottom:none; border-radius:10px 10px 0 0; display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                    <button type="button" onclick="formatDoc('bold')" class="btn-fmt" title="Tebal"><b>B</b></button>
                    <button type="button" onclick="formatDoc('italic')" class="btn-fmt" title="Miring"><i>I</i></button>
                    <button type="button" onclick="formatDoc('underline')" class="btn-fmt" title="Garis Bawah"><u>U</u></button>
                    <button type="button" onclick="formatDoc('strikeThrough')" class="btn-fmt" title="Coret"><s>S</s></button>
                    <div style="width:1px; height:16px; background:#334155; margin:0 4px;"></div>
                    <button type="button" onclick="formatDoc('formatBlock', 'H2')" class="btn-fmt" title="Heading 2"><b>H2</b></button>
                    <button type="button" onclick="formatDoc('formatBlock', 'H3')" class="btn-fmt" title="Heading 3"><b>H3</b></button>
                    <button type="button" onclick="formatDoc('formatBlock', 'P')" class="btn-fmt" title="Paragraf"><b>P</b></button>
                    <div style="width:1px; height:16px; background:#334155; margin:0 4px;"></div>
                    <button type="button" onclick="formatDoc('justifyLeft')" class="btn-fmt" title="Rata Kiri">⬅</button>
                    <button type="button" onclick="formatDoc('justifyCenter')" class="btn-fmt" title="Rata Tengah">⬇</button>
                    <button type="button" onclick="formatDoc('justifyRight')" class="btn-fmt" title="Rata Kanan">➡</button>
                    <div style="width:1px; height:16px; background:#334155; margin:0 4px;"></div>
                    <button type="button" onclick="formatDoc('insertUnorderedList')" class="btn-fmt" title="Bullet List">● List</button>
                    <button type="button" onclick="formatDoc('insertOrderedList')" class="btn-fmt" title="Number List">1. List</button>
                </div>
                
                {{-- Editor Content --}}
                <div id="editor-container" contenteditable="true" style="min-height:200px; padding:14px; border:1px solid #334155; border-radius:0 0 10px 10px; background:#0f172a; color:#e2e8f0; outline:none; overflow-y:auto; user-select:text; -webkit-user-select:text;">{!! $savedCtt !!}</div>
                <input type="hidden" id="ctt-jawaban" name="{{ $cttField }}" value="{{ $savedCtt }}">

                <button type="submit" id="submit-btn">
                    ✅ Kumpulkan Jawaban &amp; Selesai
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// ═══════════════════ CONFIG ═══════════════════
const LJK_ID          = {{ $ljk->id }};
const EXAM_TYPE       = '{{ $type }}';
const MAX_PELANGGARAN = 3;
const CSRF_TOKEN      = document.querySelector('meta[name="csrf-token"]').content;

// ─── State ───
let violationCount   = {{ $jmlPelanggaran }};
let isBlocked        = {{ ($ljk->cekal_kuliah ?? 'N') === 'Y' ? 'true' : 'false' }};
let overlayVisible   = false;
let isInitializing   = true;
let isUploading      = false;

let exitCooldown      = false; // debounce agar blur + visibilitychange tidak double-count

// ═══════════════════ INIT ═══════════════════
document.addEventListener('DOMContentLoaded', () => {
    updateViolationUI();

    if (isBlocked) {
        showBlocked();
        return;
    }

    // Tunggu interaksi pengguna (klik tombol Mulai Ujian)
    // isInitializing tetap true sampai startExam() dipanggil
});

// ═══════════════════ START EXAM ═══════════════════
function startExam() {
    // 1. Masuk fullscreen
    enterFullscreen();

    // 2. Sembunyikan layar intro
    document.getElementById('intro-screen').classList.add('hide');

    // 3. Tunda 1 detik agar transisi fullscreen selesai sebelum mengaktifkan deteksi keamanan
    setTimeout(() => { isInitializing = false; }, 1000);
}

// ═══════════════════ FULLSCREEN ═══════════════════
function enterFullscreen() {
    const el = document.documentElement;
    if      (el.requestFullscreen)           el.requestFullscreen();
    else if (el.webkitRequestFullscreen)     el.webkitRequestFullscreen();
    else if (el.mozRequestFullScreen)        el.mozRequestFullScreen();
    else if (el.msRequestFullscreen)         el.msRequestFullscreen();
}

// ═══════════════════ DETEKSI KELUAR FULLSCREEN ═══════════════════
// Menangkap SEMUA cara keluar fullscreen:
// ESC, tombol fullscreen browser, shortcut keyboard, klik di luar, dsb.
document.addEventListener('fullscreenchange',       onFsChange);
document.addEventListener('webkitfullscreenchange', onFsChange);
document.addEventListener('mozfullscreenchange',    onFsChange);
document.addEventListener('MSFullscreenChange',     onFsChange);

function onFsChange() {
    if (isInitializing || isBlocked || overlayVisible || isUploading) return;
    const inFs = !!(
        document.fullscreenElement       ||
        document.webkitFullscreenElement ||
        document.mozFullScreenElement    ||
        document.msFullscreenElement
    );
    if (!inFs) {
        onSecurityExit('Keluar dari mode layar penuh (ESC / tombol browser / dll.)');
    }
}

// ═══════════════════ DETEKSI GANTI TAB / MINIMIZE ═══════════════════
document.addEventListener('visibilitychange', () => {
    if (isInitializing || isBlocked || overlayVisible || isUploading) return;
    if (document.hidden) {
        onSecurityExit('Berpindah tab atau meminimize jendela browser');
    }
});

// ═══════════════════ DETEKSI KLIK WINDOW LAIN ═══════════════════
window.addEventListener('blur', () => {
    if (isInitializing || isBlocked || overlayVisible || isUploading) return;

    // Tunggu sebentar untuk memastikan activeElement update
    setTimeout(() => {
        // Jika yang sedang aktif/diklik adalah iframe (soal pdf/word), abaikan blur
        if (document.activeElement && document.activeElement.tagName === 'IFRAME') {
            return;
        }
        
        // Jika benar-benar pindah aplikasi, catat pelanggaran
        if (!document.hasFocus()) {
            onSecurityExit('Berpindah ke aplikasi atau jendela lain');
        }
    }, 100);
});

// ═══════════════════ ALLOW USB / WINDOWS+E ═══════════════════
function allowFocusLossForUSB() {
    isUploading = true;
    alert('Akses buka folder/USB diizinkan selama 30 detik. Silakan colok USB atau tekan Windows+E untuk buka file explorer sekarang.');
    setTimeout(() => {
        isUploading = false;
        enterFullscreen(); // Kembalikan layar penuh jika sempat keluar
    }, 30000);
}

// Blokir klik kanan
document.addEventListener('contextmenu', e => e.preventDefault());

// ═══════════════════ BLOKIR KEYBOARD BERBAHAYA ═══════════════════
document.addEventListener('keydown', e => {
    // Jika tekan Windows + E, otomatis izinkan buka folder tanpa pelanggaran
    if (e.metaKey && e.key.toLowerCase() === 'e') {
        isUploading = true;
        setTimeout(() => {
            isUploading = false;
            enterFullscreen();
        }, 30000);
    }

    const forbidden = [
        e.key === 'F12',
        (e.ctrlKey || e.metaKey) && e.shiftKey && ['I','J','C'].includes(e.key.toUpperCase()),
        (e.ctrlKey || e.metaKey) && e.key.toUpperCase() === 'U',
    ];
    if (forbidden.some(Boolean)) {
        e.preventDefault();
        e.stopPropagation();
    }
});

// Blokir klik kanan
document.addEventListener('contextmenu', e => e.preventDefault());

// ═══════════════════ LOGIKA PELANGGARAN ═══════════════════
function onSecurityExit(keterangan) {
    if (isInitializing || isBlocked || overlayVisible || exitCooldown) return;

    // Debounce: abaikan event ganda dalam 600ms
    exitCooldown = true;
    setTimeout(() => { exitCooldown = false; }, 600);

    triggerViolation(keterangan);
    
    // Otomatis kembalikan ke layar penuh
    enterFullscreen();
}

// ═══════════════════ HANDLE VIOLATION (RESMI) ═══════════════════
async function triggerViolation(keterangan) {
    if (isBlocked) return;
    violationCount++;
    updateViolationUI();

    // Tampilkan overlay peringatan resmi
    showOverlay(keterangan);

    // Kirim ke server
    try {
        const res = await fetch('{{ route('ujian.pelanggaran') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            body: JSON.stringify({
                ljk_id: LJK_ID,
                type: EXAM_TYPE,
                keterangan: keterangan,
            })
        });
        const data = await res.json();
        violationCount = data.jml_pelanggaran;
        updateViolationUI();

        if (data.cekal === 'Y') {
            isBlocked = true;
            document.getElementById('violation-overlay').classList.remove('show');
            overlayVisible = false;
            showBlocked();
        }
    } catch (err) {
        console.error('Gagal mengirim log pelanggaran:', err);
    }
}

function showOverlay(msg) {
    overlayVisible = true;
    const overlay = document.getElementById('violation-overlay');
    const count   = document.getElementById('overlay-count');
    const msgEl   = document.getElementById('overlay-msg');

    count.textContent = violationCount;
    const sisa = MAX_PELANGGARAN - violationCount;
    msgEl.innerHTML = `Terdeteksi: <strong style="color:#fecaca">${msg}</strong><br><br>
        Sisa kesempatan: <strong>${sisa > 0 ? sisa : 0}x pelanggaran</strong> sebelum diblokir.<br>
        Semua pelanggaran dicatat dan dilaporkan.`;

    overlay.classList.add('show');
    
    // Otomatis tutup overlay dan kembalikan ke layar penuh setelah 2 detik
    setTimeout(() => {
        dismissOverlay();
    }, 2000);
}

function dismissOverlay() {
    if (isBlocked) return;
    document.getElementById('violation-overlay').classList.remove('show');
    overlayVisible = false;
    // Otomatis masuk fullscreen kembali setelah dismiss
    setTimeout(enterFullscreen, 200);
}

function showBlocked() {
    document.getElementById('blocked-overlay').classList.add('show');
    if (document.exitFullscreen) document.exitFullscreen();
}

function updateViolationUI() {
    const badge = document.getElementById('violation-badge');
    const count = document.getElementById('vcount');
    count.textContent = violationCount;

    badge.classList.remove('warning', 'danger');
    if (violationCount >= MAX_PELANGGARAN) {
        badge.classList.add('danger');
    } else if (violationCount >= 2) {
        badge.classList.add('warning');
    }
}

// ═══════════════════ FILE UPLOAD ═══════════════════
let selectedFiles = [];

function handleFiles(fileList) {
    for (const f of fileList) selectedFiles.push(f);
    renderFileList();
    syncFilesToInput();
}

function handleDrop(e) {
    e.preventDefault();
    document.getElementById('upload-zone').classList.remove('dragover');
    handleFiles(e.dataTransfer.files);
}

function removeFile(idx) {
    selectedFiles.splice(idx, 1);
    renderFileList();
    syncFilesToInput();
}

function renderFileList() {
    const list = document.getElementById('file-list');
    list.innerHTML = '';
    selectedFiles.forEach((f, i) => {
        list.innerHTML += `
        <div class="file-item">
            📄 <span>${f.name}</span>
            <span style="color:#64748b;font-size:11px;">${(f.size/1024/1024).toFixed(2)} MB</span>
            <button type="button" onclick="removeFile(${i})">✕</button>
        </div>`;
    });
}

function syncFilesToInput() {
    const input = document.getElementById('file-input');
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    input.files = dt.files;
}

// Deteksi saat jendela upload dibuka agar tidak dicatat sebagai pelanggaran
document.getElementById('file-input').addEventListener('click', () => {
    isUploading = true;
    
    // Ketika jendela upload ditutup, window akan kembali fokus
    window.addEventListener('focus', function onFocus() {
        // Beri sedikit jeda agar event blur/visibilitychange tidak tidak sengaja terpicu
        setTimeout(() => {
            isUploading = false;
            // Kembalikan ke fullscreen jika saat milih file browser drop out dari fullscreen
            enterFullscreen();
        }, 800);
        window.removeEventListener('focus', onFocus);
    }, { once: true });
});

// ═══════════════════ RESIZER ═══════════════════
const resizer = document.getElementById('resizer');
const soalPanel = document.getElementById('soal-panel');
let isDragging = false;

resizer.addEventListener('mousedown', function(e) {
    isDragging = true;
    resizer.classList.add('dragging');
    document.body.classList.add('no-select');
    // Matikan pointer events di iframe agar iframe tidak 'mencuri' event mouse saat di-drag
    const iframe = document.getElementById('soal-frame');
    if (iframe) iframe.style.pointerEvents = 'none';
});

document.addEventListener('mousemove', function(e) {
    if (!isDragging) return;
    const containerOffsetLeft = document.getElementById('main').offsetLeft;
    const pointerRelativeXpos = e.clientX - containerOffsetLeft;
    const containerWidth = document.getElementById('main').offsetWidth;
    const minWidth = 100; // Lebar minimal panel
    if (pointerRelativeXpos > minWidth && pointerRelativeXpos < containerWidth - minWidth) {
        soalPanel.style.width = pointerRelativeXpos + 'px';
    }
});

document.addEventListener('mouseup', function(e) {
    if (isDragging) {
        isDragging = false;
        resizer.classList.remove('dragging');
        document.body.classList.remove('no-select');
        // Hidupkan kembali pointer events di iframe
        const iframe = document.getElementById('soal-frame');
        if (iframe) iframe.style.pointerEvents = 'auto';
    }
});

// ═══════════════════ CUSTOM RICH TEXT EDITOR ═══════════════════
function formatDoc(cmd, value = null) {
    document.execCommand(cmd, false, value);
    document.getElementById('editor-container').focus();
}

// Sinkronisasi data custom editor ke hidden input sebelum submit
document.getElementById('jawaban-form').addEventListener('submit', function(e) {
    if (isBlocked) { e.preventDefault(); return; }
    
    // Ambil isi editor
    document.getElementById('ctt-jawaban').value = document.getElementById('editor-container').innerHTML;

    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '⏳ Mengumpulkan jawaban...';
});
</script>
</body>
</html>
