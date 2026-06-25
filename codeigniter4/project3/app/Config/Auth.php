<?php
namespace Config;
use CodeIgniter\Config\BaseConfig;
use Myth\Auth\Config\Auth as AuthConfig;
class Auth extends AuthConfig
{
    /**
     * ---------------------------------------------------------------
     * Views used by Auth Controllers
     * ---------------------------------------------------------------
     */
    public $views = [
        'login'           => 'auth/login',
        'register'        => 'Myth\Auth\Views\register',
        'forgot'          => 'Myth\Auth\Views\forgot',
        'reset'           => 'Myth\Auth\Views\reset',
        'emailForgot'     => 'Myth\Auth\Views\emails\forgot',
        'emailActivation' => 'Myth\Auth\Views\emails\activation',
    ];

    /**
     * ---------------------------------------------------------------
     * Require Confirmation Registration via Email
     * ---------------------------------------------------------------
     */
    public $requireActivation = null;
}
