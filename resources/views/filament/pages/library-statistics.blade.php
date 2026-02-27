<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @livewire(\App\Filament\Widgets\LibraryVisitorChart::class)
        @livewire(\App\Filament\Widgets\LibraryLoanChart::class)
        @livewire(\App\Filament\Widgets\LibraryProcurementChart::class)
    </div>
</x-filament-panels::page>