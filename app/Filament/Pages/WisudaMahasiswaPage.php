<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\WisudaMahasiswa;
use App\Models\PeriodeWisuda;
use App\Models\RiwayatPendidikan;
use BackedEnum;
use Filament\Schemas\Schema;
use UnitEnum;

class WisudaMahasiswaPage extends Page implements HasForms
{
    use InteractsWithForms;
    use \BezhanSalleh\FilamentShield\Traits\HasPageShield;

    protected string $view = 'filament.pages.wisuda-mahasiswa-page';
    protected static ?string $title = 'Informasi Wisuda';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static string|UnitEnum|null $navigationGroup = 'Tugas Akhir';
    public ?array $data = [];
    public $riwayatAktif;
    public $wisudaData;
    public $periodeWisudas;



    protected function getFormModel(): WisudaMahasiswa
    {
        return $this->wisudaData ?? new WisudaMahasiswa();
    }

    public function mount()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $this->riwayatAktif = $user->siswaData?->riwayatPendidikanAktif;

        if ($this->riwayatAktif) {
            $this->wisudaData = WisudaMahasiswa::where('id_riwayat_pendidikan', $this->riwayatAktif->id)->first();

            if ($this->wisudaData) {
                $this->form->fill($this->wisudaData);
            } else {
                $this->form->fill([
                    'email' => $user->email,
                    'no_hp' => $this->riwayatAktif->siswaData->no_telepon,
                ]);
            }
        }

        $this->periodeWisudas = PeriodeWisuda::where('tahun', date('Y'))->orderBy('periode_ke')->get();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->model($this->getFormModel())
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('nama_arab')
                            ->label('Nama Lengkap (Huruf Arab)')
                            ->placeholder('أسر المعالي')
                            ->helperText('Tulis menggunakan keyboard Arab atau paste di sini'),

                        TextInput::make('tempat_lahir_arab')
                            ->label('Tempat Lahir (Huruf Arab)')
                            ->placeholder('تولوع اكوع'),

                        TextInput::make('alamat_malang')
                            ->label('Alamat di Malang')
                            ->required(),

                        TextInput::make('no_hp')
                            ->label('No. Handphone Aktif')
                            ->tel()
                            ->required(),

                        TextInput::make('email')
                            ->label('Email Aktif')
                            ->email()
                            ->required(),

                        FileUpload::make('pas_foto')
                            ->label('Pas Foto Berwarna (Latar Putih)')
                            ->image()
                            ->directory('wisuda/foto')
                            ->required()
                            ->helperText('Maksimal 1 MB, format .png .jpg .jpeg'),

                        Select::make('id_pembimbing_1')
                            ->label('Dosen Pembimbing 1')
                            ->relationship('pembimbing1', 'nama')
                            ->searchable()
                            ->preload(),

                        Select::make('id_pembimbing_2')
                            ->label('Dosen Pembimbing 2')
                            ->relationship('pembimbing2', 'nama')
                            ->searchable()
                            ->preload(),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        $data = $this->form->getState();

        if (!$this->riwayatAktif) return;

        if (!$this->wisudaData) {
            // Cek ketersediaan periode yang buka
            $periodeOpen = PeriodeWisuda::where('status', 'Buka')->first();
            if (!$periodeOpen) {
                Notification::make()
                    ->title('Gagal mendaftar')
                    ->body('Mohon maaf, pendaftaran wisuda saat ini belum dibuka.')
                    ->danger()
                    ->send();
                return;
            }

            if ($periodeOpen->pendaftar_count >= $periodeOpen->kuota) {
                Notification::make()
                    ->title('Kuota Penuh')
                    ->body('Mohon maaf, kuota periode ini sudah terpenuhi.')
                    ->warning()
                    ->send();
                return;
            }

            $data['id_riwayat_pendidikan'] = $this->riwayatAktif->id;
            $data['id_periode_wisuda'] = $periodeOpen->id;
            $data['status_pendaftaran'] = 'Proses';

            $this->wisudaData = WisudaMahasiswa::create($data);

            $periodeOpen->increment('pendaftar_count');

            Notification::make()
                ->title('Pendaftaran Sukses')
                ->body('Anda telah berhasil mendaftar wisuda periode ' . $periodeOpen->periode_ke)
                ->success()
                ->send();
        } else {
            $this->wisudaData->update($data);

            Notification::make()
                ->title('Data Terupdate')
                ->body('Informasi pendaftaran wisuda Anda telah diperbarui.')
                ->success()
                ->send();
        }
    }

    public function getClearanceStatus()
    {
        return [
            'prodi' => [
                'met' => $this->wisudaData?->bebas_prodi ?? false,
                'title' => 'Bebas Tanggungan Jurusan/Prodi',
                'points' => [
                    'Mahasiswa telah mengisi judul Tugas Akhir (Skripsi / Tesis / Disertasi)',
                    'Mahasiswa mendaftar ujian Tugas Akhir dan menyerahkan berkas komprehensif',
                    'Mahasiswa telah melakukan ujian Tugas Akhir dan dinyatakan lulus',
                    'Mahasiswa memiliki nilai Tugas Akhir terinput dalam SIAKAD',
                    'Menyerahkan sertifikat TOEFL dan TOAFL',
                    'Jumlah SKS lulus terpenuhi sesuai ketentuan',
                ]
            ],
            'fakultas' => [
                'met' => $this->wisudaData?->bebas_fakultas ?? false,
                'title' => 'Bebas Tanggungan Fakultas',
                'points' => [
                    'Mahasiswa terdaftar dalam SK Yudisium',
                    'Mahasiswa menyerahkan Softcopy & Hardcopy Skripsi dijilid',
                    'Tidak memiliki tanggungan apapun di Fakultas',
                ]
            ],
            'perpustakaan' => [
                'met' => $this->wisudaData?->bebas_perpustakaan ?? false,
                'title' => 'Bebas Tanggungan Perpustakaan',
                'points' => [
                    'Mengembalikan seluruh pinjaman buku',
                    'Mengunggah File Skripsi di E-Theses',
                    'Tidak memiliki tanggungan di Perpustakaan',
                ]
            ],
            'keuangan' => [
                'met' => $this->wisudaData?->bebas_keuangan ?? false,
                'title' => 'Bebas Tanggungan Keuangan',
                'points' => [
                    'Membayar seluruh biaya pendidikan sesuai ketentuan',
                    'Membayar biaya wisuda (Khusus Pascasarjana)',
                    'Tidak memiliki tanggungan keuangan',
                ]
            ],
        ];
    }
}
