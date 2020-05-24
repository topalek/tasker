<?php

namespace app\core;


class Input
{
    public static function exists($type = 'post')
    {
        switch ($type) {
            case 'post':
                return ! empty($_POST);
                break;
            case 'get':
                return ! empty($_GET);
                break;
            default:
                return false;
                break;
        }
    }

    public static function is($item)
    {
        if (isset($_POST[$item])) {
            return $_POST[$item];
        }elseif ($_GET[$item]) {
            return $_GET[$item];
        }

        return '';
    }

    public static function post($name = null)
    {
        if ( ! $name) {
            return $_POST;
        }

        return $_POST[$name] ?? null;
    }

    public static function get($name = null)
    {
        if ( ! $name) {
            return $_GET;
        }

        return $_GET[$name] ?? null;
    }

}