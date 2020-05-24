<?php


namespace app\core;


class Session
{
    public static function get($name)
    {
        return $_SESSION[$name] ?? null;
    }

    public static function delete($name)
    {
        unset($_SESSION[$name]);
    }

    public static function flash($name, $message = '')
    {
        self::put("flash", [$name => $message]);
    }

    public static function put($name, $value)
    {
        return $_SESSION[$name] = $value;
    }
}