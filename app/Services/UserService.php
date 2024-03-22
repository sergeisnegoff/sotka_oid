<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public static function usersPinnedOnManager(User $user) {
        $contact =$user->managerContact;
        //if ($user->id == 2594) dd($user);
        if (!$contact) return [];
        return User::where('manager_id', $contact->id);
    }
}
