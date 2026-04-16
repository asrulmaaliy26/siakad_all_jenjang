<x-filament-panels::page>
    <div class="space-y-6">
        <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                Dokumentasi Alur SIAKAD
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Pilih tab di bawah untuk melihat alur proses utama dalam sistem SIAKAD.
            </p>
        </div>

        <x-filament::section>
            <div
                x-data="{ tab: 'pendaftaran' }"
                class="space-y-6"
            >
                <div class="inline-flex rounded-lg border border-gray-200 bg-gray-50 p-1 text-xs font-medium text-gray-600 dark:border-gray-700 dark:bg-gray-900/60 dark:text-gray-300">
                    <button
                        type="button"
                        class="px-3 py-1.5 rounded-md transition"
                        :class="tab === 'pendaftaran'
                            ? 'bg-white text-primary-600 shadow-sm dark:bg-gray-800 dark:text-primary-400'
                            : 'hover:bg-white/60 dark:hover:bg-gray-800/70'"
                        @click="tab = 'pendaftaran'"
                    >
                        Alur Pendaftaran
                    </button>
                    <button
                        type="button"
                        class="px-3 py-1.5 rounded-md transition"
                        :class="tab === 'krs'
                            ? 'bg-white text-primary-600 shadow-sm dark:bg-gray-800 dark:text-primary-400'
                            : 'hover:bg-white/60 dark:hover:bg-gray-800/70'"
                        @click="tab = 'krs'"
                    >
                        Alur Pengambilan KRS
                    </button>
                    <button
                        type="button"
                        class="px-3 py-1.5 rounded-md transition"
                        :class="tab === 'semester'
                            ? 'bg-white text-primary-600 shadow-sm dark:bg-gray-800 dark:text-primary-400'
                            : 'hover:bg-white/60 dark:hover:bg-gray-800/70'"
                        @click="tab = 'semester'"
                    >
                        Alur Perubahan Semester & KRS
                    </button>
                    <button
                        type="button"
                        class="px-3 py-1.5 rounded-md transition"
                        :class="tab === 'dosen'
                            ? 'bg-white text-primary-600 shadow-sm dark:bg-gray-800 dark:text-primary-400'
                            : 'hover:bg-white/60 dark:hover:bg-gray-800/70'"
                        @click="tab = 'dosen'"
                    >
                        Panduan Penggunaan Dosen
                    </button>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm ring-1 ring-black/5 backdrop-blur-sm dark:border-gray-800 dark:bg-gray-900">
                    {{-- Tab: Pendaftaran --}}
                    <div
                        x-show="tab === 'pendaftaran'"
                        x-cloak
                    >
                        @include('filament.pages.dokumentasi.alur-pendaftaran')
                    </div>

                    {{-- Tab: KRS --}}
                    <div
                        x-show="tab === 'krs'"
                        x-cloak
                    >
                        @include('filament.pages.dokumentasi.alur-krs')
                    </div>

                    {{-- Tab: Perubahan Semester & KRS --}}
                    <div
                        x-show="tab === 'semester'"
                        x-cloak
                    >
                        @include('filament.pages.dokumentasi.alur-semester')
                    </div>

                    {{-- Tab: Panduan Dosen --}}
                    <div
                        x-show="tab === 'dosen'"
                        x-cloak
                    >
                        @include('filament.pages.dokumentasi.panduan-dosen')
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>

