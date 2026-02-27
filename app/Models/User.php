<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

use Filament\Models\Contracts\HasAvatar;
use Illuminate\Support\Facades\Storage;

/**
 * @method bool isMurid()
 * @method bool isPendaftar()
 * @method bool isPengajar()
 * @method bool isAdmin()
 */
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    public function canAccessPanel(Panel $panel): bool
    {
        // Logic akses panel, biasanya true untuk development 
        // atau cek role tertentu
        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->isMurid() && $this->siswaData && $this->siswaData->foto_profil) {
            return Storage::url($this->siswaData->foto_profil);
        }

        if ($this->isPengajar() && $this->dosenData && $this->dosenData->foto_profil) {
            return Storage::url($this->dosenData->foto_profil);
        }

        return null;
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
        'view_password',
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
            && !$this->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function isPendaftar(): bool
    {
        return $this->hasRole('pendaftar');
    }

    /**
     * Cek apakah user adalah pengajar murni (tanpa role lain)
     */
    public function isPengajar(): bool
    {
        return $this->hasRole('pengajar')
            && !$this->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    /**
     * Cek apakah user adalah admin (memiliki role admin)
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
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
