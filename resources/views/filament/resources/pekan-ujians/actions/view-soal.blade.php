<div class="flex flex-col w-full max-w-7xl mx-auto gap-6 p-6">
    @php
    $fileKey = $type == 'uas' ? 'soal_uas' : 'soal_uts';
    $noteKey = $type == 'uas' ? 'ctt_soal_uas' : 'ctt_soal_uts';

    $filePath = $record->$fileKey;
    $note = $record->$noteKey;
    $typeName = strtoupper($type);
    $fileUrl = $filePath ? asset('storage/' . $filePath) : null;
    $extension = $filePath ? pathinfo($filePath, PATHINFO_EXTENSION) : null;
    @endphp

    <!-- Info Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Jenis Ujian Card -->
        <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-academic-cap class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jenis Ujian</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $typeName }}</p>
                </div>
            </div>
        </div>

        <!-- Status Card -->
        <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
                    <div class="mt-1">
                        @php
                        $statusKey = $type == 'uas' ? 'status_uas' : 'status_uts';
                        $statusValue = $record->$statusKey;
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                            {{ ($statusValue == 'Y' || $statusValue === true) ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ ($statusValue == 'Y' || $statusValue === true) ? 'bg-green-600' : 'bg-red-600' }}"></span>
                            {{ ($statusValue == 'Y' || $statusValue === true) ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Info Card -->
        <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-document class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">File Soal</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                        @if($filePath)
                        <span class="text-green-600 dark:text-green-400">Tersedia</span>
                        @else
                        <span class="text-gray-400 dark:text-gray-500">Tidak Ada</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Container File Soal - HANYA TAMPIL JIKA ADA FILE -->
    @if($filePath)
    <div class="w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header Preview -->
        <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <x-heroicon-o-document-text class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Preview File Soal</span>
                <span class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs px-2 py-0.5 rounded-full uppercase ml-2">{{ $extension }}</span>
            </div>
            <a href="{{ $fileUrl }}" target="_blank"
                class="inline-flex items-center px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white rounded-md transition-colors text-sm font-medium shadow-sm">
                <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4 mr-1.5" />
                Buka di Tab Baru
            </a>
        </div>

        <!-- Preview Content -->
        <div class="relative" style="height: 600px;">
            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
            <div class="w-full h-full flex items-center justify-center overflow-auto p-4 bg-gray-100 dark:bg-gray-900">
                <img src="{{ $fileUrl }}" alt="Preview Soal {{ $typeName }}"
                    class="max-w-full max-h-full w-auto h-auto object-contain rounded shadow-lg">
            </div>
            @elseif(strtolower($extension) === 'pdf')
            <iframe
                src="{{ $fileUrl }}#toolbar=0&navpanes=0&scrollbar=1&view=FitH"
                class="w-full h-full"
                style="border: none;"
                frameborder="0"
                allowfullscreen>
            </iframe>
            @else
            <div class="flex flex-col items-center justify-center h-full p-8 text-center bg-gray-100 dark:bg-gray-900">
                <x-heroicon-o-document class="w-20 h-20 text-gray-400 dark:text-gray-600 mb-4" />
                <p class="text-gray-600 dark:text-gray-400 mb-4">File tidak dapat dipreview secara langsung.</p>
                <a href="{{ $fileUrl }}" download
                    class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors text-sm font-medium shadow-lg">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                    Download File
                </a>
            </div>
            @endif
        </div>

        <!-- File Metadata -->
        <div class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-3 flex justify-between items-center text-xs">
            <div class="flex items-center space-x-4">
                <span class="text-gray-500 dark:text-gray-400">
                    <span class="font-medium">Nama File:</span>
                    <span class="text-gray-700 dark:text-gray-300 ml-1">{{ basename($filePath) }}</span>
                </span>
                <span class="text-gray-500 dark:text-gray-400">
                    <span class="font-medium">Ukuran:</span>
                    <span class="text-gray-700 dark:text-gray-300 ml-1">--</span>
                </span>
            </div>
            <span class="text-gray-400 dark:text-gray-500">Diakses: {{ now()->format('d M Y H:i') }}</span>
        </div>
    </div>
    @endif

    <!-- Section Catatan - TETAP TAMPIL, MENJADI FOKUS UTAMA JIKA TIDAK ADA FILE -->
    @if($note)
    <div class="w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden {{ !$filePath ? 'mt-0' : '' }}">
        <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                <h3 class="font-medium text-gray-900 dark:text-white text-sm">
                    Catatan Soal
                </h3>
                @php
                $cleanText = strip_tags($note);
                $wordCount = str_word_count($cleanText);
                @endphp
                <span class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs px-2 py-0.5 rounded-full">
                    {{ $wordCount }} kata
                </span>
            </div>
            @if(!$filePath)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                <x-heroicon-o-star class="w-3 h-3 mr-1" />
                Fokus Utama
            </span>
            @endif
        </div>
        <div class="p-6 max-h-[400px] overflow-y-auto bg-white dark:bg-gray-800">
            <div class="prose prose-sm max-w-none dark:prose-invert text-gray-700 dark:text-gray-300 leading-relaxed">
                {!! $note !!}
            </div>
        </div>
    </div>
    @elseif(!$filePath)
    <!-- Empty State - Tampil jika TIDAK ADA FILE DAN TIDAK ADA CATATAN -->
    <div class="w-full bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700 p-12 text-center">
        <div class="flex flex-col items-center justify-center">
            <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <x-heroicon-o-document-minus class="w-10 h-10 text-gray-400 dark:text-gray-500" />
            </div>
            <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Belum Ada Data Soal</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-md">
                File soal dan catatan belum diunggah untuk ujian ini. Silakan hubungi pengajar untuk informasi lebih lanjut.
            </p>
        </div>
    </div>
    @endif

    <!-- Input LJK Mahasiswa (Livewire Component) -->
    <div class="mt-6">
        @livewire('filament.resources.pekan-ujians.components.input-ljk-minimal', ['record' => $record, 'type' => $type], 'input-ljk-' . $record->id)
    </div>
</div>

<!-- Tambahkan CSS untuk meningkatkan tampilan select di dark mode -->
<style>
    /* Custom style for dark mode select options */
    .dark select option {
        background-color: #1f2937;
        color: #f3f4f6;
    }

    /* Custom scrollbar for dark mode content */
    .dark .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .dark .overflow-y-auto::-webkit-scrollbar-track {
        background: #374151;
        border-radius: 4px;
    }

    .dark .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #4b5563;
        border-radius: 4px;
    }

    .dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }
</style>