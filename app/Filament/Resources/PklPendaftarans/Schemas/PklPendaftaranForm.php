<?php

namespace App\Filament\Resources\PklPendaftarans\Schemas;

use App\Models\PklLembaga;
use App\Models\PklPeriode;
use App\Models\SiswaData;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class PklPendaftaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Pendaftaran')
                    ->columns(2)
                    ->schema([
                        Select::make('id_pkl_periode')
                            ->label('Periode PKL')
                            ->options(PklPeriode::where('is_active', true)->pluck('nama', 'id'))
                            ->required()
                            ->reactive()
                            ->disabled(fn($record) => $record !== null && !Auth::user()->isAdmin()),

                        Select::make('id_pkl_lembaga')
                            ->label('Lembaga')
                            ->options(function ($get) {
                                $periodeId = $get('id_pkl_periode');
                                if (!$periodeId) return [];
                                
                                $periode = PklPeriode::find($periodeId);
                                if (!$periode) return [];

                                return $periode->lembagas->mapWithKeys(function ($lembaga) use ($periodeId) {
                                    $terisi = $lembaga->pendaftarans()->where('id_pkl_periode', $periodeId)->count();
                                    $kuota = $lembaga->pivot->kuota ?? 0;
                                    $sisa = $kuota - $terisi;
                                    return [$lembaga->id => "{$lembaga->nama} (Sisa: {$sisa})"];
                                });
                            })
                            ->required()
                            ->disabled(fn($record) => $record !== null && !Auth::user()->isAdmin()),

                        Select::make('id_siswa_data')
                            ->label('Mahasiswa')
                            ->options(SiswaData::pluck('nama', 'id'))
                            ->searchable()
                            ->required()
                            ->default(function () {
                                $user = Auth::user();
                                return ($user && $user->isMurid()) ? $user->getSiswaId() : null;
                            })
                            ->disabled(fn() => !Auth::user()->isAdmin())
                            ->dehydrated(),

                        ToggleButtons::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->colors([
                                'pending' => 'gray',
                                'approved' => 'success',
                                'rejected' => 'danger',
                            ])
                            ->inline()
                            ->default('pending')
                            ->visible(fn() => Auth::user()->isAdmin())
                            ->dehydrated(),
                    ]),
            ]);
    }
}
