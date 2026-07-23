<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasRoleBasedTableScope
{
    /**
     * Scope a table query based on the user's role.
     * Students (Murid) see only their own data.
     * 
     * @param Builder $query
     * @param string $riwayatRelation The dot-notation relationship string to siswaData, e.g., 'riwayatPendidikan.siswaData'
     * @return Builder
     */
    public static function applyRoleBasedTableScope(Builder $query, string $riwayatRelation = 'riwayatPendidikan.siswaData'): Builder
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user && method_exists($user, 'isMurid') && $user->isMurid()) {
            $query->whereHas($riwayatRelation, function (Builder $q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query;
    }
}
