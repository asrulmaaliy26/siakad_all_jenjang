<?php

namespace App\Filament\Resources\SiswaData\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SiswaDataOrangTuaRelationManager extends RelationManager
{
    protected static string $relationship = 'orangTua';
    protected static ?string $title = 'Data Orang Tua';

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Data Orang Tua')
                    ->tabs([
                        Tabs\Tab::make('Data Ayah')
                            ->schema([
                                Forms\Components\TextInput::make('Nama_Ayah')->label('Nama Ayah')->maxLength(255),
                                Forms\Components\TextInput::make('Tempat_Lhr_Ayah')->label('Tempat Lahir'),
                                Forms\Components\DatePicker::make('Tgl_Lhr_Ayah')->label('Tanggal Lahir'),
                                Forms\Components\TextInput::make('Agama_Ayah')->label('Agama'),
                                Forms\Components\TextInput::make('Gol_Darah_Ayah')->label('Gol. Darah'),
                                Forms\Components\TextInput::make('Pendidikan_Terakhir_Ayah')->label('Pendidikan Terakhir'),
                                Forms\Components\TextInput::make('Pekerjaan_Ayah')->label('Pekerjaan'),
                                Forms\Components\TextInput::make('Penghasilan_Ayah')->label('Penghasilan'),
                                Forms\Components\TextInput::make('Kebutuhan_Khusus_Ayah')->label('Kebutuhan Khusus'),
                                Forms\Components\TextInput::make('Nomor_KTP_Ayah')->label('No KTP'),
                                Forms\Components\Textarea::make('Alamat_Ayah')->label('Alamat')->columnSpanFull(),
                                Forms\Components\TextInput::make('No_HP_ayah')->label('No HP')->tel(),

                                // Detail Alamat Ayah
                                Section::make('Detail Alamat Ayah')
                                    ->schema([
                                        Forms\Components\TextInput::make('No_Rmh_Ayah')->label('No Rumah'),
                                        Forms\Components\TextInput::make('Dusun_Ayah')->label('Dusun'),
                                        Forms\Components\TextInput::make('RT_Ayah')->label('RT'),
                                        Forms\Components\TextInput::make('RW_Ayah')->label('RW'),
                                        Forms\Components\TextInput::make('Desa_Ayah')->label('Desa'),
                                        Forms\Components\TextInput::make('Kec_Ayah')->label('Kecamatan'),
                                        Forms\Components\TextInput::make('Kab_Ayah')->label('Kabupaten'),
                                        Forms\Components\TextInput::make('Kode_Pos_Ayah')->label('Kode Pos'),
                                        Forms\Components\TextInput::make('Prov_Ayah')->label('Provinsi'),
                                        Forms\Components\TextInput::make('Kewarganegaraan_Ayah')->label('Kewarganegaraan'),
                                    ])->collapsible()->collapsed(),
                            ])->columns(2),

                        Tabs\Tab::make('Data Ibu')
                            ->schema([
                                Forms\Components\TextInput::make('Nama_Ibu')->label('Nama Ibu')->maxLength(255),
                                Forms\Components\TextInput::make('Tempat_Lhr_Ibu')->label('Tempat Lahir'),
                                Forms\Components\DatePicker::make('Tgl_Lhr_Ibu')->label('Tanggal Lahir'),
                                Forms\Components\TextInput::make('Agama_Ibu')->label('Agama'),
                                Forms\Components\TextInput::make('Gol_Darah_Ibu')->label('Gol. Darah'),
                                Forms\Components\TextInput::make('Pendidikan_Terakhir_Ibu')->label('Pendidikan Terakhir'),
                                Forms\Components\TextInput::make('Pekerjaan_Ibu')->label('Pekerjaan'),
                                Forms\Components\TextInput::make('Penghasilan_Ibu')->label('Penghasilan'),
                                Forms\Components\TextInput::make('Kebutuhan_Khusus_Ibu')->label('Kebutuhan Khusus'),
                                Forms\Components\TextInput::make('Nomor_KTP_Ibu')->label('No KTP'),
                                Forms\Components\Textarea::make('Alamat_Ibu')->label('Alamat')->columnSpanFull(),
                                Forms\Components\TextInput::make('No_HP_ibu')->label('No HP')->tel(),

                                // Detail Alamat Ibu
                                Section::make('Detail Alamat Ibu')
                                    ->schema([
                                        Forms\Components\TextInput::make('No_Rmh_Ibu')->label('No Rumah'),
                                        Forms\Components\TextInput::make('Dusun_Ibu')->label('Dusun'),
                                        Forms\Components\TextInput::make('RT_Ibu')->label('RT'),
                                        Forms\Components\TextInput::make('RW_Ibu')->label('RW'),
                                        Forms\Components\TextInput::make('Desa_Ibu')->label('Desa'),
                                        Forms\Components\TextInput::make('Kec_Ibu')->label('Kecamatan'),
                                        Forms\Components\TextInput::make('Kab_Ibu')->label('Kabupaten'),
                                        Forms\Components\TextInput::make('Kode_Pos_Ibu')->label('Kode Pos'),
                                        Forms\Components\TextInput::make('Prov_Ibu')->label('Provinsi'),
                                        Forms\Components\TextInput::make('Kewarganegaraan_Ibu')->label('Kewarganegaraan'),
                                    ])->collapsible()->collapsed(),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Nama_Ayah')
            ->columns([
                Tables\Columns\TextColumn::make('Nama_Ayah')->label('Ayah'),
                Tables\Columns\TextColumn::make('No_HP_ayah')->label('No HP Ayah'),
                Tables\Columns\TextColumn::make('Nama_Ibu')->label('Ibu'),
                Tables\Columns\TextColumn::make('No_HP_ibu')->label('No HP Ibu'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                // ->visible(fn() => ! auth()->user()?->isMurid() || auth()->user()?->isPengajar()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => ! auth()->user()?->isMurid() || auth()->user()?->isPengajar()),
                ]),
            ]);
    }
}
