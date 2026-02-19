<x-filament-panels::page>
    <form wire:submit.prevent="download">
        {{ $this->form }}

        <div class="mt-6 flex gap-x-3">
            <x-filament::button type="submit" color="primary">
                Download ZIP
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>