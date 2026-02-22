<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetActiveJenjangMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = \Filament\Facades\Filament::auth()->user();

        if ($user) {
            // Priority 1: Super Admin (Don't force, rely on switcher/session)
            if ($user->hasRole('super_admin')) {
                // Do nothing, let the switcher or existing session handle it
            }
            // Priority 2: Murid (Force based on educational history)
            elseif ($user->isMurid()) {
                $siswa = \App\Models\SiswaData::where('user_id', $user->id)->first();
                if ($siswa) {
                    $riwayat = $siswa->riwayatPendidikanAktif;
                    $jenjangId = null;

                    if ($riwayat && $riwayat->id_jurusan) {
                        $jenjangId = \App\Models\Jurusan::find($riwayat->id_jurusan)?->id_jenjang_pendidikan;
                    } elseif ($siswa->pendaftar && $siswa->pendaftar->id_jurusan) {
                        $jenjangId = \App\Models\Jurusan::find($siswa->pendaftar->id_jurusan)?->id_jenjang_pendidikan;
                    }

                    if ($jenjangId) {
                        session(['active_jenjang_id' => $jenjangId]);
                    }
                }
            }
            // Priority 3: Admin Jenjang (Force based on role name)
            else {
                $roles = $user->getRoleNames();
                foreach ($roles as $role) {
                    if (str_starts_with($role, 'admin_jenjang_')) {
                        $slug = str_replace('admin_jenjang_', '', $role);
                        $jenjangMatch = \App\Models\JenjangPendidikan::all()->first(function ($j) use ($slug) {
                            return \Illuminate\Support\Str::slug($j->nama) === $slug;
                        });

                        if ($jenjangMatch) {
                            session(['active_jenjang_id' => $jenjangMatch->id]);
                            break;
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
