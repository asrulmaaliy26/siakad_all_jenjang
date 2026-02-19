<div class="flex items-center">
    @if(\Filament\Facades\Filament::auth()->user()?->hasRole('super_admin'))
    <div class="relative group">
        <label for="jenjang_switch" class="sr-only">Pilih Jenjang</label>
        <div class="flex items-center space-x-2 bg-white/50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700 p-1">
            <span class="pl-2 text-xs font-medium text-gray-500 dark:text-gray-400">
                Jenjang:
            </span>
            <select
                id="jenjang_switch"
                wire:model.live="activeJenjangId"
                class="
                    block w-auto border-none bg-transparent py-1 pl-1 pr-8 text-sm font-semibold text-primary-600 
                    focus:ring-0 sm:text-sm sm:leading-6 cursor-pointer
                    dark:text-primary-400
                ">
                @foreach($jenjangs as $jenjang)
                <option value="{{ $jenjang->id }}" class="text-gray-900 dark:text-white bg-white dark:bg-gray-800">
                    {{ $jenjang->nama }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    @endif
</div>