    @php
    $user = \Filament\Facades\Filament::auth()->user();
    $isMurid = $user && $user->hasRole('murid');
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
            @foreach($record->siswaDataLjk as $ljk)
            @if($ljk->akademikKrs && $ljk->akademikKrs->riwayatPendidikan && $ljk->akademikKrs->riwayatPendidikan->siswaData)
            <option value="{{ $ljk->akademikKrs->riwayatPendidikan->siswaData->id }}">
                {{ $ljk->akademikKrs->riwayatPendidikan->siswaData->nama }}
                ({{ $ljk->akademikKrs->riwayatPendidikan->nomor_induk }})
            </option>
            @endif
            @endforeach
        </select>
    </div>
    @endif

    @if($selectedStudentId && $this->getSelectedLjkRecord())
    @php
    $selectedLjk = $this->getSelectedLjkRecord();
    $student = $selectedLjk->akademikKrs->riwayatPendidikan->siswaData;
    $nim = $selectedLjk->akademikKrs->riwayatPendidikan->nomor_induk;
    @endphp

    @if(!$isMurid)
    <!-- Selected Student Info (Only for Admin/Pengajar) -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-r-lg">
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
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form wire:submit="save">
            {{ $this->form }}

            <div class="mt-6 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <x-heroicon-o-check class="w-5 h-5 mr-2" />
                    Simpan Data LJK
                </button>
            </div>
        </form>
    </div>
    @endif
    </div>