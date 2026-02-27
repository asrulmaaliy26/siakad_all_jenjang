<?php

namespace App\Helpers;

class SiakadTerm
{
    public static function pengajar(): string
    {
        return 'Dosen';
    }

    public static function pesertaDidik(): string
    {
        return 'Mahasiswa';
    }

    public static function mataPelajaran(): string
    {
        return 'Mata Kuliah';
    }

    public static function mataPelajaranKelas(): string
    {
        return 'Perkuliahan';
    }
}
