<div class="space-y-4">
    <div class="text-sm text-gray-500">
        Daftar mata pelajaran yang diambil oleh mahasiswa ini.
    </div>

    @livewire('siswa-data-ljk-modal', ['recordId' => $record->id, 'excludeTaken' => $excludeTaken ?? false], 'subjects-' . $record->id)
</div>