<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Kunjungan Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden transform transition-all">
        <div class="p-8 text-center">
            @if($status === 'success')
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Check-in Berhasil</h1>
            <p class="text-slate-600 mb-6">{{ $message }}</p>

            @if(isset($student))
            <div class="bg-slate-50 rounded-2xl p-4 mb-6 border border-slate-100 italic">
                <p class="text-sm text-slate-500 uppercase tracking-widest font-semibold mb-1">Mahasiswa</p>
                <p class="text-lg font-bold text-indigo-600">{{ $student->nama }}</p>
            </div>
            @endif
            @else
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Check-in Gagal</h1>
            <p class="text-slate-600 mb-6">{{ $message }}</p>
            @endif

            <div class="space-y-3">
                <a href="/" class="block w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold transition-colors shadow-lg shadow-indigo-200">
                    Kembali ke Beranda
                </a>
                <p class="text-xs text-slate-400">Pencatatan dilakukan secara otomatis oleh sistem</p>
            </div>
        </div>
    </div>
</body>

</html>