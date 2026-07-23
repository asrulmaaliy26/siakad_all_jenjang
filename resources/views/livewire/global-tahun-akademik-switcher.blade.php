<div class="flex items-center gap-2 px-3" x-data x-on:reload-page.window="window.location.href = window.location.pathname + '?_clear=' + new Date().getTime()">
    <label for="global_ta" class="text-sm font-medium text-gray-700 dark:text-gray-200 hidden md:block">
        Tahun Ajaran:
    </label>
    <select 
        id="global_ta"
        wire:model.live="tahunAkademikId" 
        class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 min-w-[150px] cursor-pointer"
    >
        @foreach($tahunAkademiks as $ta)
            <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
        @endforeach
    </select>
</div>
