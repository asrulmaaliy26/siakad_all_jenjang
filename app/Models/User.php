<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, \App\Traits\HasJenjangScope;

    public function scopeByJenjang($query, $jenjangId)
    {
        $jenjang = \App\Models\JenjangPendidikan::find($jenjangId);
        $activeSlug = $jenjang ? \Illuminate\Support\Str::slug($jenjang->nama) : 'unknown';

        return $query->where(function ($q) use ($jenjangId, $activeSlug) {
            // Case 1: User has SiswaData -> check jenjang in SiswaData
            $q->whereHas('siswaData', function ($sub) use ($jenjangId) {
                // We use withoutGlobalScopes to ensure we are filtering accurately here
                $sub->withoutGlobalScopes()->where(function ($ss) use ($jenjangId) {
                    $ss->whereHas('pendaftar.jurusan', function ($j) use ($jenjangId) {
                        $j->where('id_jenjang_pendidikan', $jenjangId);
                    })
                        ->orWhereHas('riwayatPendidikan.jurusan', function ($j) use ($jenjangId) {
                            $j->where('id_jenjang_pendidikan', $jenjangId);
                        });
                });
            })
                // Case 2: User has DosenData -> check jenjang in DosenData
                ->orWhereHas('dosenData', function ($sub) use ($jenjangId) {
                    $sub->withoutGlobalScopes()->whereHas('jurusan', function ($subJurusan) use ($jenjangId) {
                        $subJurusan->where('id_jenjang_pendidikan', $jenjangId);
                    });
                })
                // Case 3: User has NEITHER SiswaData nor DosenData record at all (Universal User like Admin)
                ->orWhere(function ($sub) use ($activeSlug) {
                    $sub->whereDoesntHave('siswaData', function ($sq) {
                        $sq->withoutGlobalScopes();
                    })
                        ->whereDoesntHave('dosenData', function ($dq) {
                            $dq->withoutGlobalScopes();
                        })
                        ->where(function ($adminCheck) use ($activeSlug) {
                            // If they have any "admin_jenjang_" role, it MUST match the active slug
                            $adminCheck->whereDoesntHave('roles', function ($rq) {
                                $rq->where('name', 'like', 'admin_jenjang_%');
                            })
                                ->orWhereHas('roles', function ($rq) use ($activeSlug) {
                                    $rq->where('name', 'admin_jenjang_' . $activeSlug);
                                });
                        });
                });
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Logic akses panel, biasanya true untuk development 
        // atau cek role tertentu
        return true;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function siswaData()
    {
        return $this->hasOne(SiswaData::class, 'user_id');
    }

    public function dosenData()
    {
        return $this->hasOne(DosenData::class, 'user_id');
    }

    public function isMurid(): bool
    {
        return $this->hasRole('murid')
            && !$this->hasAnyRole(['super_admin', 'admin', 'admin_jenjang', 'kaprodi']);
    }

    /**
     * Cek apakah user adalah pengajar murni (tanpa role lain)
     */
    public function isPengajar(): bool
    {
        return $this->hasRole('pengajar')
            && !$this->hasAnyRole(['super_admin', 'admin', 'admin_jenjang', 'kaprodi']);
    }

    /**
     * Cek apakah user adalah admin (memiliki role admin)
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin', 'admin_jenjang', 'kaprodi']);
    }

    /**
     * Get ID dosen berdasarkan user_id
     */
    public function getDosenId()
    {
        return DosenData::where('user_id', $this->id)->value('id');
    }
    public function ulasans()
    {
        return $this->hasMany(Ulasan::class);
    }
}
