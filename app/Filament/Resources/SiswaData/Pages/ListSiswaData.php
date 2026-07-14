<?php

namespace App\Filament\Resources\SiswaData\Pages;

use App\Filament\Resources\SiswaData\SiswaDataResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;

class ListSiswaData extends ListRecords
{
    protected static string $resource = SiswaDataResource::class;



    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('download_arsip')
                ->label('Download Arsip')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn(): string => SiswaDataResource::getUrl('download-files'))
                ->disabled(fn() => !\Filament\Facades\Filament::auth()->user()?->hasAnyRole(['super_admin', 'admin'])),
            Action::make('export_pddikti')
                ->label('Export PDDIKTI')
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->action(function () {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\MahasiswaPddiktiExport,
                        'mahasiswa_pddikti_' . date('Ymd_His') . '.xlsx'
                    );
                })
                ->requiresConfirmation()
                ->modalHeading('Export Data Mahasiswa (PDDIKTI)')
                ->modalDescription('Apakah Anda yakin ingin mengekspor seluruh data mahasiswa ke dalam format Excel PDDIKTI?'),
            Action::make('import_pddikti')
                ->label('Import PDDIKTI')
                ->icon('heroicon-o-document-arrow-up')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('File Excel PDDIKTI')
                        ->disk('local')
                        ->directory('imports')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $filePath = storage_path('app/private/' . $data['file']);
                    try {
                        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\MahasiswaPddiktiImport, $filePath);
                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil di-import')
                            ->body('Data mahasiswa PDDIKTI berhasil disinkronisasi.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Gagal meng-import')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->modalHeading('Import Data Mahasiswa (PDDIKTI)')
                ->modalDescription('Unggah file template Excel PDDIKTI (format .xlsx) yang telah diisi untuk menambahkan/menyinkronkan data mahasiswa.'),
        ];
    }
}
