<?php

namespace App\Helpers;

use App\Models\JenjangPendidikan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class SiakadTerm
{
    protected static function getJenjangType(): string
    {
        $activeJenjangId = Session::get('active_jenjang_id');

        if (!$activeJenjangId) {
            return 'sekolah'; // Default fallback
        }

        // Cache hasil query per-id untuk efisiensi
        return Cache::remember("jenjang_type_{$activeJenjangId}", 60, function () use ($activeJenjangId) {
            $jenjang = JenjangPendidikan::find($activeJenjangId);
            return $jenjang ? strtolower($jenjang->type ?? 'sekolah') : 'sekolah';
        });
    }

    public static function pengajar(): string
    {
        $type = self::getJenjangType();
        return $type === 'kampus' ? 'Dosen' : 'Guru';
    }

    public static function pesertaDidik(): string
    {
        $type = self::getJenjangType();
        return $type === 'kampus' ? 'Mahasiswa' : 'Siswa';
    }

    public static function mataPelajaran(): string
    {
        $type = self::getJenjangType();
        return $type === 'kampus' ? 'Mata Kuliah' : 'Mata Pelajaran';
    }

    public static function mataPelajaranKelas(): string
    {
        $type = self::getJenjangType();
        return $type === 'kampus' ? 'Perkuliahan' : 'Pelajaran';
    }
}
