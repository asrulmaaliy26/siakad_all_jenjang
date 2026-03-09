<x-filament-panels::page>
    <div class="fi-modal-content-inner">
        @if($dosenId)
        @livewire('krs-advisor-chat', ['dosenId' => $dosenId])
        @else
        <div class="flex flex-col items-center justify-center p-8 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-full mb-4">
                <x-heroicon-o-user-group class="w-12 h-12 text-gray-400" />
            </div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">Belum Memiliki Pembimbing</h3>
            <p class="text-gray-500 dark:text-gray-400 text-center max-w-md">
                Anda belum memiliki Dosen Wali/Pembimbing yang terdaftar di sistem. Mohon hubungi Bagian Akademik untuk informasi lebih lanjut.
            </p>
        </div>
        @endif
    </div>
</x-filament-panels::page>