<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JenjangPendidikan;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class SyncJenjangRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jenjang:sync-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Spatie roles for each Jenjang Pendidikan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing roles for Jenjang Pendidikan...');

        foreach (JenjangPendidikan::all() as $jenjang) {
            $roleName = 'admin_jenjang_' . Str::slug($jenjang->nama);

            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);

            $this->line('Role synced: ' . $roleName);
        }

        $this->info('Sync completed!');
    }
}
