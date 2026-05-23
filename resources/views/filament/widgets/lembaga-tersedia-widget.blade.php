<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                    {{ $activePeriode ? 'Lembaga PKL Tersedia (' . $activePeriode->nama . ')' : 'Informasi Pendaftaran PKL' }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $activePeriode ? 'Pilih lembaga yang sesuai dengan minat Anda.' : '' }}
                </p>
            </div>
            {{--
            @if($activePeriode && !$hasRegistered)
                <x-filament::button
                    color="primary"
                    icon="heroicon-m-plus"
                    tag="a"
                    href="{{ \App\Filament\Resources\PklPendaftarans\PklPendaftaranResource::getUrl('create') }}"
            >
            Daftar Sekarang
            </x-filament::button>
            @endif
            --}}
        </div>

        @if(!$activePeriode)
        <div class="p-6 text-center border-2 border-dashed rounded-xl border-gray-200 dark:border-gray-800">
            <div class="flex flex-col items-center justify-center space-y-2">
                <x-filament::icon
                    icon="heroicon-o-calendar-days"
                    class="w-12 h-12 text-gray-400" />
                <div class="text-lg font-medium text-gray-950 dark:text-white">
                    Periode belum dibuka
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Booking lembaga belum tersedia saat ini. Silakan hubungi admin untuk informasi lebih lanjut.
                </p>
            </div>
        </div>
        @elseif($hasRegistered)
        <div class="p-4 bg-success-50 dark:bg-success-950/20 rounded-xl border border-success-200 dark:border-success-800">
            <div class="flex items-center space-x-3 text-success-700 dark:text-success-400">
                <x-filament::icon icon="heroicon-m-check-circle" class="w-5 h-5" />
                <span class="font-medium">Anda sudah melakukan pendaftaran PKL untuk periode ini.</span>
            </div>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($lembagas as $lembaga)
            <div class="relative p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">{{ $lembaga['nama'] }}</h3>
                        <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400 mb-4">
                            <x-filament::icon icon="heroicon-m-globe-alt" class="w-3 h-3" />
                            <a href="{{ $lembaga['website'] }}" target="_blank" class="hover:underline">{{ $lembaga['website'] }}</a>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Sisa Kuota</span>
                            <span class="text-lg font-bold {{ $lembaga['sisa'] > 0 ? 'text-primary-600' : 'text-danger-600' }}">
                                {{ $lembaga['sisa'] }} / {{ $lembaga['kuota'] }}
                            </span>
                        </div>

                        @if($lembaga['sisa'] > 0)
                        <x-filament::button
                            color="primary"
                            size="xs"
                            wire:click="daftar({{ $lembaga['id'] }})"
                            wire:loading.attr="disabled">
                            Pilih
                        </x-filament::button>
                        @else
                        <x-filament::badge color="danger">Penuh</x-filament::badge>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>