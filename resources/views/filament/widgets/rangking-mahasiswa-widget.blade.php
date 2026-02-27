<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <!-- Ranking Angkatan -->
        <x-filament::section>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-primary-100 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400 rounded-full">
                    <x-filament::icon
                        icon="heroicon-m-trophy"
                        class="h-8 w-8" />
                </div>
                <div>
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Peringkat Angkatan
                    </h2>
                    <p class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                        #{{ $rankAngkatan }}
                        <span class="text-sm font-normal text-gray-500">
                            dari {{ $totalAngkatan }}
                        </span>
                    </p>
                </div>
            </div>
        </x-filament::section>

        <!-- Ranking Keseluruhan -->
        <x-filament::section>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-warning-100 text-warning-600 dark:bg-warning-900/50 dark:text-warning-400 rounded-full">
                    <x-filament::icon
                        icon="heroicon-m-globe-americas"
                        class="h-8 w-8" />
                </div>
                <div>
                    <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        Peringkat Seluruh Mahasiswa
                    </h2>
                    <p class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                        #{{ $rankKeseluruhan }}
                        <span class="text-sm font-normal text-gray-500">
                            dari {{ $totalKeseluruhan }}
                        </span>
                    </p>
                </div>
            </div>
        </x-filament::section>

    </div>
</x-filament-widgets::widget>