<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaPengajuanJudul extends Model
{
    use HasFactory;

    protected $table = 'ta_pengajuan_judul';

    protected $fillable = [
        'id_tahun_akademik',
        'id_riwayat_pendidikan',
        'judul',
        'abstrak',
        'id_dosen_review',
        'tgl_pengajuan',
        'tgl_ujian',
        'ruangan_ujian',
        'tgl_acc_judul',
        'file',
        'id_dosen_pembimbing_1',
        'id_dosen_pembimbing_2',
        'id_dosen_pembimbing_3',
        'status_dosen_1',
        'status_dosen_2',
        'status_dosen_3',
        'nilai_dosen_1',
        'nilai_dosen_2',
        'nilai_dosen_3',
        'file_revisi_dosen_1',
        'file_revisi_dosen_2',
        'file_revisi_dosen_3',
        'ctt_revisi_dosen_1',
        'ctt_revisi_dosen_2',
        'ctt_revisi_dosen_3',
        'status',
    ];

    protected $casts = [
        'tgl_pengajuan'  => 'date',
        'tgl_ujian'      => 'date',
        'tgl_acc_judul'  => 'date',
        'nilai_dosen_1'  => 'decimal:2',
        'nilai_dosen_2'  => 'decimal:2',
        'nilai_dosen_3'  => 'decimal:2',
    ];

    // ── Relasi ke tahun akademik ──────────────────────────────────────────
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik');
    }

    // ── Relasi ke riwayat pendidikan (mahasiswa) ──────────────────────────
    public function riwayatPendidikan()
    {
        return $this->belongsTo(RiwayatPendidikan::class, 'id_riwayat_pendidikan');
    }

    // ── Relasi ke dosen reviewer ──────────────────────────────────────────
    public function dosenReview()
    {
        return $this->belongsTo(DosenData::class, 'id_dosen_review');
    }

    // ── Relasi ke dosen pembimbing 1, 2, 3 ───────────────────────────────
    public function dosenPembimbing1()
    {
        return $this->belongsTo(DosenData::class, 'id_dosen_pembimbing_1');
    }

    public function dosenPembimbing2()
    {
        return $this->belongsTo(DosenData::class, 'id_dosen_pembimbing_2');
    }

    public function dosenPembimbing3()
    {
        return $this->belongsTo(DosenData::class, 'id_dosen_pembimbing_3');
    }

    // ── Scope helpers ─────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeByMahasiswa($query, int $idRiwayatPendidikan)
    {
        return $query->where('id_riwayat_pendidikan', $idRiwayatPendidikan);
    }

    public function scopeByDosen($query, int $idDosen)
    {
        return $query->where(function ($q) use ($idDosen) {
            $q->where('id_dosen_review', $idDosen)
                ->orWhere('id_dosen_pembimbing_1', $idDosen)
                ->orWhere('id_dosen_pembimbing_2', $idDosen)
                ->orWhere('id_dosen_pembimbing_3', $idDosen);
        });
    }

    // ── Accessor helpers ──────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'Menunggu Review',
            'disetujui'  => 'Disetujui',
            'ditolak'    => 'Ditolak',
            'revisi'     => 'Perlu Revisi',
            'selesai'    => 'Selesai',
            default      => ucfirst($this->status ?? '-'),
        };
    }

    public function getNilaiRataRataAttribute(): ?float
    {
        $values = array_filter([
            $this->nilai_dosen_1,
            $this->nilai_dosen_2,
            $this->nilai_dosen_3,
        ], fn($v) => $v !== null);

        if (empty($values)) {
            return null;
        }

        return round(array_sum($values) / count($values), 2);
    }
}
