<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public static function usersPinnedOnManager(User $user) {
        //dd($user);
        $contact =$user->managerContact;

        if (!$contact) return [];
        return User::where('manager_id', $contact->id);
    }
}
