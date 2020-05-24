<?php

namespace app\core;


class Security
{
    public static function generateCsrf()
    {
        if (Session::get('_csrf')) {
            return Session::get('_csrf');
        }

        return Session::put('_csrf', static::salt(32));
    }

    public static function salt($length)
    {
        return bin2hex(random_bytes($length));
    }

    public static function checkCsrf($token)
    {
        if (Session::get('_csrf') == $token) {
            Session::delete('_csrf');

            return true;
        }

        return false;
    }

    public static function generatePassword($password, $salt = '')
    {
        return hash('sha256', $password . $salt);
    }
}