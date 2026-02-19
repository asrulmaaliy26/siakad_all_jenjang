<?php

namespace App\Models\RefOption;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramKelas extends Model
{
    use HasFactory;

    /**
     * Tabel yang digunakan
     */
    protected $table = 'reference_option';

    /**
     * Field yang bisa diisi
     */
    protected $fillable = [
        'nama_grup',
        'kode',
        'nilai',
        'status',
        'deskripsi',
    ];

    /**
     * Global scope untuk hanya mengambil grup "Program Kelas"
     */
    protected static function booted()
    {
        static::addGlobalScope('program_kelas', function ($query) {
            $query->where('nama_grup', 'program_kelas');
        });
    }

    /**
     * Getter alias "nama" agar sesuai konsep ProgramKelas
     */
    public function getNamaAttribute()
    {
        return $this->nilai; // nilai di reference_option dipakai sebagai nama
    }

    /**
     * Getter untuk deskripsi (hanya alias, optional)
     */
    public function getDeskripsiAttribute()
    {
        return $this->deskripsi;
    }

    /**
     * Relasi ke tabel Kelas
     * Misal, satu ProgramKelas bisa punya banyak Kelas
     */
    public function kelas()
    {
        return $this->hasMany(\App\Models\Kelas::class, 'ro_program_kelas');
    }

    /**
     * Scope tambahan jika ingin filter aktif saja
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 1);
    }
}

// use App\Models\ProgramKelas;

// // Ambil semua Ruang Kelas
// $ProgramKelas = ProgramKelas::all();

// // Bisa dipakai relasi
// foreach ($ProgramKelas as $r) {
//     echo $r->nama; // otomatis ambil dari 'nilai'
//     foreach ($r->mataPelajaranKelas as $mp) {
//         echo $mp->nama_mapel;
//     }
// }

// // Bisa buat query
// $ruang = ProgramKelas::where('kode', 'R101')->first();
// > use App\Models\RefOption\ProgramKelas;
// > $programKelas = ProgramKelas::all();
// = Illuminate\Database\Eloquent\Collection {#3092
//     all: [
//       App\Models\RefOption\ProgramKelas {#7532
//         id: 157,
//         nama_grup: "program_kelas",
//         kode: "A",
//         nilai: "Kelas A",
//         status: "Y",
//         deskripsi: null,
//         created_at: "2023-07-31 07:32:24",
//         updated_at: "2025-09-15 08:06:42",
//       },
//       App\Models\RefOption\ProgramKelas {#7528       
//         id: 276,
//         nama_grup: "program_kelas",
//         kode: "C",
//         nilai: "Kelas C",
//         status: "Y",
//         deskripsi: null,
//         created_at: "2025-09-25 02:57:50",
//         updated_at: "2025-09-29 11:49:39",
//       },
//     ],
//   }

// > \App\Models\RefOption\ProgramKelas::all();
// = Illuminate\Database\Eloquent\Collection {#7120     
//     all: [
//       App\Models\RefOption\ProgramKelas {#7111       
//         id: 157,
//         nama_grup: "program_kelas",
//         kode: "A",
//         nilai: "Kelas A",
//         status: "Y",
//         deskripsi: null,
//         created_at: "2023-07-31 07:32:24",
//         updated_at: "2025-09-15 08:06:42",
//       },
//       App\Models\RefOption\ProgramKelas {#7522       
//         id: 276,
//         nama_grup: "program_kelas",
//         kode: "C",
//         nilai: "Kelas C",
//         status: "Y",
//         deskripsi: null,
//         created_at: "2025-09-25 02:57:50",
//         updated_at: "2025-09-29 11:49:39",
//       },
//     ],
//   }

// > cls

//    Error  Undefined constant "cls".

// > clear
// > $pk = \App\Models\RefOption\ProgramKelas::first(); 
// = App\Models\RefOption\ProgramKelas {#7513
//     id: 157,
//     nama_grup: "program_kelas",
//     kode: "A",
//     nilai: "Kelas A",
//     status: "Y",
//     deskripsi: null,
//     created_at: "2023-07-31 07:32:24",
//     updated_at: "2025-09-15 08:06:42",
//   }

// > $pk;
// = App\Models\RefOption\ProgramKelas {#7513
//     id: 157,
//     nama_grup: "program_kelas",
//     kode: "A",
//     nilai: "Kelas A",
//     status: "Y",
//     deskripsi: null,
//     created_at: "2023-07-31 07:32:24",
//     updated_at: "2025-09-15 08:06:42",
//   }

// > $pk->kelas;
// = Illuminate\Database\Eloquent\Collection {#7619
//     all: [],
//   }

// > $pk->kelas()->create([
// .     'nama' => 'Kelas A1',
// .     'semester' => 1,
// .     'id_jenjang_pendidikan' => 1,
// .     'id_tahun_akademik' => 1,
// .     'id_jurusan' => 1,
// .     'status_aktif' => 1,
// . ]);
// = App\Models\Kelas {#7149
//     semester: 1,
//     id_jenjang_pendidikan: 1,
//     id_tahun_akademik: 1,
//     id_jurusan: 1,
//     status_aktif: 1,
//     id_program_kelas: 157,
//     updated_at: "2026-01-20 16:55:06",
//     created_at: "2026-01-20 16:55:06",
//     id: 5,
//   }

// > $pk->kelas;
// = Illuminate\Database\Eloquent\Collection {#7619     
//     all: [],
//   }

// > $pk->kelas()->where('status_aktif', 1)->get();     
// = Illuminate\Database\Eloquent\Collection {#7622     
//     all: [
//       App\Models\Kelas {#7165
//         id: 5,
//         id_program_kelas: "157",
//         semester: 1,
//         id_jenjang_pendidikan: 1,
//         id_tahun_akademik: 1,
//         id_jurusan: 1,
//         status_aktif: "Y",
//         created_at: "2026-01-20 16:55:06",
//         updated_at: "2026-01-20 16:55:06",
//       },
//     ],
//   }

// >