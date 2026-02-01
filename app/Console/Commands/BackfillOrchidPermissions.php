<?php

namespace App\Console\Commands;

use App\Orchid\Models\Role;
use Illuminate\Console\Command;
use App\Models\User;

class BackfillOrchidPermissions extends Command
{
    protected $signature = 'orchid:backfill-permissions';
    protected $description = 'Sync Orchid permissions based on Voyager role';

    public function handle(): int
    {
        $count = 0;
        $customerRole = Role::find(1);
        $adminRole = Role::where('slug', 'admin')->first();
        User::query()->chunkById(200, function ($users) use (&$count, $customerRole, $adminRole) {
            foreach ($users as $user) {
                $roleName = $user->role?->name ?? null;

                if (!$roleName && isset($user->role_id)) {
                    $roleName = \DB::table('roles')->where('id', $user->role_id)->value('name');
                }

                $isAdmin = in_array($roleName, ['admin','administrator','superadmin'], true);

//                $mustHave = $isAdmin
//                    ? [
//                        'platform.index'          => 1,
//                        'platform.systems'        => 1,
//                        'platform.systems.roles'  => 1,
//                        'platform.systems.users'  => 1,
//                    ]
//                    : [];
//
//                $current = is_array($user->permissions ?? null) ? $user->permissions : [];
//                $merged  = $current + $mustHave;
//
//                if ($merged !== $current) {
//                    $user->permissions = $merged;
//                    $user->save();
//                    $count++;
//                }

                if (!$isAdmin && !$user->inRole($customerRole)) {
                    $user->addRole($customerRole);
                } elseif ($isAdmin && !$user->inRole($adminRole)) {
                    $user->addRole($adminRole);
                }
            }
        });

        $this->info("Updated users: {$count}");
        return self::SUCCESS;
    }
}
