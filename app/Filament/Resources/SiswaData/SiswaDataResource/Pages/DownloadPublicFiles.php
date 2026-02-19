<?php

namespace App\Filament\Resources\SiswaData\SiswaDataResource\Pages;

use App\Filament\Resources\SiswaData\SiswaDataResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DownloadPublicFiles extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = SiswaDataResource::class;

    protected string $view = 'filament.resources.siswa-data.siswa-data-resource.pages.download-public-files';

    protected static ?string $title = 'Download Arsip File';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Pilih Folder untuk Di-download')
                    ->description('Silakan pilih folder (jenjang pendidikan) yang ingin di-download sebagai arsip ZIP.')
                    ->schema([
                        Select::make('folder')
                            ->label('Folder / Jenjang Pendidikan')
                            ->options(function () {
                                // List directories in 'public/uploads'
                                // Assuming structure: storage/app/public/uploads/{FOLDER}
                                $directories = Storage::disk('public')->directories('uploads');
                                $options = [];
                                foreach ($directories as $dir) {
                                    $name = basename($dir);
                                    $options[$dir] = $name; // Key: full path relative to disk root, Value: folder name
                                }

                                // Also check root directories just in case uploads is not the only place
                                // Or stick to what helper does? Helper puts in 'uploads/...'
                                // Let's just create options from 'uploads' subdirectories for now.
                                return $options;
                            })
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function download()
    {
        try {
            $data = $this->form->getState();
            $folderPath = $data['folder'];
            $folderName = basename($folderPath);

            // Full path on filesystem
            $fullPath = Storage::disk('public')->path($folderPath);

            if (!File::isDirectory($fullPath)) {
                throw new \Exception('Folder tidak ditemukan.');
            }

            // Create Zip
            $zipFileName = 'archive-' . Str::slug($folderName) . '-' . date('YmdHis') . '.zip';
            $zipPath = storage_path('app/public/temp_zips/' . $zipFileName);

            // Ensure temp dir exists
            if (!File::exists(dirname($zipPath))) {
                File::makeDirectory(dirname($zipPath), 0755, true);
            }

            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                // Using RecursiveDirectoryIterator to get all files
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($fullPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        // Relative path inside zip
                        $relativePath = substr($filePath, strlen($fullPath) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }

                $zip->close();
            } else {
                throw new \Exception('Gagal membuat file ZIP.');
            }

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Terjadi Kesalahan')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
