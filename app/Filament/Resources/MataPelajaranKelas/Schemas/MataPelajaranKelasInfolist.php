<?php

// namespace App\Filament\Resources\MataPelajaranKelas\Schemas;

// use Filament\Schemas\Infolist;
// use Filament\Infolists\Components\TextEntry;
// use Filament\Schemas\Components\Section;

// class MataPelajaranKelasInfolist
// {
//     public static function configure(Infolist $infolist): Infolist
//     {
//         return $infolist
//             ->schema([
//                 Section::make('Informasi Dasar')
//                     ->schema([
//                         TextEntry::make('mataPelajaranKurikulum.mataPelajaranMaster.name')
//                             ->label('Mata Pelajaran'),
//                         TextEntry::make('kelas.semester')
//                             ->label('Semester'),
//                         TextEntry::make('jumlah')
//                             ->label('Jumlah Mahasiswa'),
//                         TextEntry::make('dosenData.nama')
//                             ->label('Dosen Pengajar'),
//                     ])->columns(2),
//                 Section::make('Jadwal')
//                     ->schema([
//                         TextEntry::make('hari'),
//                         TextEntry::make('jam'),
//                         TextEntry::make('ruangKelas.nilai')
//                             ->label('Ruang Kelas'),
//                     ])->columns(3),
//             ]);
//     }
// }
