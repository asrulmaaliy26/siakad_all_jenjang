<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferalCode extends Model
{
    protected $fillable = ['nama', 'kode', 'keterangan', 'type', 'status'];

    public function pendaftars()
    {
        return $this->hasMany(SiswaDataPendaftar::class, 'id_referal_code');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->kode)) {
                $model->kode = strtoupper(\Illuminate\Support\Str::random(8));
            }
        });
    }
}
