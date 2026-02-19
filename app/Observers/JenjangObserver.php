<?php

namespace App\Observers;

use App\Models\JenjangPendidikan;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class JenjangObserver
{
    /**
     * Handle the JenjangPendidikan "created" event.
     */
    public function created(JenjangPendidikan $jenjang): void
    {
        $this->syncRole($jenjang);
    }

    /**
     * Handle the JenjangPendidikan "updated" event.
     */
    public function updated(JenjangPendidikan $jenjang): void
    {
        $this->syncRole($jenjang);
    }

    /**
     * Handle the JenjangPendidikan "deleted" event.
     */
    public function deleted(JenjangPendidikan $jenjang): void
    {
        $roleName = 'admin_jenjang_' . \Illuminate\Support\Str::slug($jenjang->nama);
        Role::where('name', $roleName)->delete();
    }

    protected function syncRole(JenjangPendidikan $jenjang): void
    {
        $roleName = 'admin_jenjang_' . \Illuminate\Support\Str::slug($jenjang->nama);

        Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web'
        ]);
    }
}
