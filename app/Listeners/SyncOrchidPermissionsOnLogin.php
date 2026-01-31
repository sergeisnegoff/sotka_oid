<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\User;

class SyncOrchidPermissionsOnLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        // На всякий: работаем только с нашим User
        if (!$user instanceof User) {
            return;
        }

        // 1) Определяем "Voyager admin"
        // Voyager обычно хранит primary role через role_id и relation role()
        $voyagerRoleName = null;

        try {
            // если relation есть
            $voyagerRoleName = $user->role?->name ?? null;
        } catch (\Throwable $e) {
            // игнор
        }

        // fallback, если role relation не работает: попробуем role_id -> roles таблица (Voyager)
        if (!$voyagerRoleName && isset($user->role_id)) {
            $voyagerRoleName = \DB::table('roles')->where('id', $user->role_id)->value('name');
        }

        $isVoyagerAdmin = in_array($voyagerRoleName, ['admin', 'administrator', 'superadmin'], true);

        // 2) Синхроним Orchid permissions
        // (можешь расширять список, но начни с системного доступа)
        $mustHave = $isVoyagerAdmin
            ? [
                'platform.index'          => 1,
                'platform.systems'        => 1,
                'platform.systems.roles'  => 1,
                'platform.systems.users'  => 1,
            ]
            : [
            ];

        $current = is_array($user->permissions ?? null) ? $user->permissions : [];
        $merged  = $current + $mustHave; // не затираем существующие ключи

        // пишем только если реально есть изменения
        if ($merged !== $current) {
            $user->permissions = $merged;
            $user->save();
        }
    }
}
