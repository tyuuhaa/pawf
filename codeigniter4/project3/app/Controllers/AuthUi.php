<?php

namespace App\Controllers;

class AuthUi extends BaseController
{
    public function login()
    {
        return view('auth/login', [
            'config' => config('Auth'),
            'initialTab' => 'login',
        ]);
    }

    public function register()
    {
        return view('auth/login', [
            'config' => config('Auth'),
            'initialTab' => 'register',
        ]);
    }
}
