<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RefOption\JabatanFungsional;
use App\Models\RefOption\PangkatGolongan;
use App\Models\RefOption\Agama;
use App\Models\RefOption\StatusDosen;

class DosenData extends Model
{
    use HasFactory, \App\Traits\HasJenjangScope;

    protected $table = 'dosen_data';

    public function scopeByJenjang($query, $jenjangId)
    {
        // Path: dosen_data -> jurusan -> id_jenjang_pendidikan
        return $query->whereHas('jurusan', function ($q) use ($jenjangId) {
            $q->where('id_jenjang_pendidikan', $jenjangId);
        });
    }
    protected $fillable = [
        'foto_profil',
        'nama',
        'NIPDN',
        'NIY',
        'gelar_depan',
        'gelar_belakang',
        'ro_pangkat_gol',
        'ro_jabatan',
        'id_jurusan',
        'email',
        'tanggal_lahir',
        'jenis_kelamin',
        'ibu_kandung',
        'kewarganegaraan',
        'Alamat',
        'status_kawin',
        'ro_status_dosen',
        'ro_agama',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mataPelajaranKelas()
    {
        return $this->hasMany(MataPelajaranKelas::class, 'id_dosen_data');
    }
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan');
    }

    // Relasi ke Reference Options
    public function jabatanFungsional()
    {
        return $this->belongsTo(JabatanFungsional::class, 'ro_jabatan');
    }

    public function pangkat()
    {
        return $this->belongsTo(PangkatGolongan::class, 'ro_pangkat_gol');
    }

    public function statusDosen()
    {
        return $this->belongsTo(StatusDosen::class, 'ro_status_dosen');
    }
    public function agama()
    {
        return $this->belongsTo(Agama::class, 'ro_agama');
    }

    public function dokumen()
    {
        return $this->hasMany(DosenDokumen::class, 'id_dosen');
    }

    // ── Relasi ke Tugas Akhir ─────────────────────────────────────────────
    public function pengajuanJudulDireview()
    {
        return $this->hasMany(TaPengajuanJudul::class, 'id_dosen_review');
    }

    public function pengajuanJudulDibimbing()
    {
        // Semua pengajuan yang dosen ini menjadi pembimbing (1, 2, atau 3)
        return TaPengajuanJudul::where('id_dosen_pembimbing_1', $this->id)
            ->orWhere('id_dosen_pembimbing_2', $this->id)
            ->orWhere('id_dosen_pembimbing_3', $this->id);
    }

    public function seminarProposalDibimbing()
    {
        return TaSeminarProposal::where('id_dosen_pembimbing_1', $this->id)
            ->orWhere('id_dosen_pembimbing_2', $this->id)
            ->orWhere('id_dosen_pembimbing_3', $this->id);
    }

    public function skripsiDibimbing()
    {
        return TaSkripsi::where('id_dosen_pembimbing_1', $this->id)
            ->orWhere('id_dosen_pembimbing_2', $this->id)
            ->orWhere('id_dosen_pembimbing_3', $this->id);
    }
}
