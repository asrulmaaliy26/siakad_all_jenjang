<?php

namespace App\Exports;

use App\Exports\Sheets\MataPelajaranKelasDataSheet;
use App\Exports\Sheets\MataPelajaranKelasDosenRefSheet;
use App\Exports\Sheets\MataPelajaranKelasHariRefSheet;
use App\Exports\Sheets\MataPelajaranKelasPelaksanaanRefSheet;
use App\Exports\Sheets\MataPelajaranKelasRuangRefSheet;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MataPelajaranKelasExport implements WithMultipleSheets
{
    protected Collection $records;
    protected array $selectedColumns;

    /**
     * Semua kolom yang tersedia beserta label untuk CheckboxList.
     */
    public static function allColumns(): array
    {
        return [
            // === KUNCI IDENTIFIKASI ===
            'id'                   => 'id',
            'kode_feeder'          => 'kode_feeder',

            // === REFERENSI (tidak diimport) ===
            'mata_pelajaran'       => 'Nama Mata Pelajaran',
            'program_kelas'        => 'Program Kelas',
            'dosen_nama'           => 'Nama Dosen',
            'ruang'                => 'Ruang Kelas (nama)',
            'pelaksanaan'          => 'Pelaksanaan (nama)',

            // === DATA EDITABLE ===
            'id_dosen_data'        => 'id_dosen_data',
            'ro_ruang_kelas'       => 'ro_ruang_kelas',
            'ro_pelaksanaan_kelas' => 'ro_pelaksanaan_kelas',
            'jumlah'               => 'jumlah',
            'hari'                 => 'hari',
            'tanggal'              => 'tanggal',
            'jam'                  => 'jam',
            'uts'                  => 'uts',
            'uas'                  => 'uas',
            'status_uts'           => 'status_uts',
            'status_uas'           => 'status_uas',
            'ruang_uts'            => 'ruang_uts',
            'ruang_uas'            => 'ruang_uas',
            'link_kelas'           => 'link_kelas',
            'passcode'             => 'passcode',
        ];
    }

    public function __construct(Collection $records, array $selectedColumns)
    {
        $this->records         = $records;
        $this->selectedColumns = $selectedColumns;
    }

    public function sheets(): array
    {
        return [
            new MataPelajaranKelasDataSheet($this->records, $this->selectedColumns),
            new MataPelajaranKelasDosenRefSheet(),
            new MataPelajaranKelasRuangRefSheet(),
            new MataPelajaranKelasPelaksanaanRefSheet(),
            new MataPelajaranKelasHariRefSheet(),
        ];
    }
}
