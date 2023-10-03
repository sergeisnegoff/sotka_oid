<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
    }

    public function resetPassword(Request $request) {
        $data = $request->validate([
            'email' => 'required'
        ]);

        User::sendResetPasswordMail($data['email']);

        return response()->json([
            'success' => 'Ваш новый пароль отправлен на почту!'
        ]);
    }
}
