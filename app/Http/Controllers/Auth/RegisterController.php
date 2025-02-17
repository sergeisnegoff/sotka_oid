<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\signUp;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'phon' => ['required', 'unique:users'],
            'city' => 'required',
            'password' => [
                'required',
                Password::min(8) // Минимум 8 символов
                ->mixedCase() // Требует буквы в верхнем и нижнем регистре
                ->numbers(), // Требует цифры
                'confirmed',
            ],
            'agree' => 'required',
            'smart-token' => 'required',
        ], [
            'smart-token' => 'Поставьте галочку что Вы не робот..',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        if (isset($data['email'])) {
            Mail::to([$data['email'], 'info@sotka-sem.ru'])->send(new signUp([
                'email' => $data['email'],
                'name' => $data['name'],
                'password' => $data['password']
            ]));
        }
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phon' => $data['phon'],
            'city' => $data['city'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
