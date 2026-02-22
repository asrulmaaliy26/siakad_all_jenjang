<div>
    @php
    $user = \Filament\Facades\Filament::auth()->user();
    $isMurid = $user && $user->isMurid();
    @endphp

    @if(!$isMurid)
    <!-- Daftar Mahasiswa Dropdown -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <label for="student-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Pilih Mahasiswa:
        </label>
        <select wire:model.live="selectedStudentId" id="student-select"
            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            <option value="">-- Pilih Mahasiswa --</option>
            @foreach($record->siswaDataLjk()->withoutGlobalScopes()->get() as $ljk)
            @php
            $riwayat = $ljk->akademikKrs()->withoutGlobalScopes()->first()?->riwayatPendidikan()->withoutGlobalScopes()->first();
            $siswaData = $riwayat?->siswaData()->withoutGlobalScopes()->first();
            @endphp
            @if($siswaData)
            <option value="{{ $siswaData->id }}">
                {{ $siswaData->nama }}
                ({{ $riwayat->nomor_induk }})
            </option>
            @endif
            @endforeach
        </select>
    </div>
    @endif

    <div class="mt-6">
        @if($selectedStudentId && ($selectedLjk = $this->getSelectedLjkRecord()))
        @php
        $riwayat = $selectedLjk->akademikKrs()->withoutGlobalScopes()->first()?->riwayatPendidikan()->withoutGlobalScopes()->first();
        $student = $riwayat?->siswaData()->withoutGlobalScopes()->first();
        $nim = $riwayat?->nomor_induk;
        @endphp

        @if(!$isMurid)
        <!-- Selected Student Info (Only for Admin/Pengajar) -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-r-lg mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-heroicon-o-user class="h-5 w-5 text-blue-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        Input LJK untuk: <span class="font-bold">{{ $student->nama }}</span> ({{ $nim }})
                    </h3>
                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                        Silakan upload file LJK dan isi catatan jika diperlukan.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Form Input LJK -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
            wire:key="student-form-{{ $selectedStudentId ?? 'none' }}">
            <div class="space-y-6">
                {{ $this->form }}

                <div class="mt-6 flex justify-end">
                    <button type="button"
                        wire:click.prevent="submitForm"
                        wire:loading.attr="disabled"
                        wire:target="submitForm"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <x-heroicon-o-check wire:loading.remove wire:target="submitForm" class="w-5 h-5 mr-2" />
                        <svg wire:loading wire:target="submitForm" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="submitForm">Simpan Data LJK</span>
                        <span wire:loading wire:target="submitForm">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
        @elseif(!$isMurid)
        <!-- Empty State for Admin -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center"
            wire:key="admin-empty-state">
            <div class="flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                    <x-heroicon-o-magnifying-glass-circle class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                </div>
                <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Belum Ada Mahasiswa Terpilih</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-md">
                    Silakan pilih mahasiswa dari daftar di atas untuk melihat dan mengelola data Lembar Jawab Komputer (LJK) mereka.
                </p>
            </div>
        </div>
        @endif
    </div>
</div>