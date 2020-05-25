<?php


namespace app\core;


class Session
{
    public static function get($name)
    {
        if ($pos = strrpos($name, '.') !== false) {
            return self::getValue($_SESSION, $name);
        }

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

    public static function valid()
    {
        $sid = null;
        if ( ! empty(self::get('user'))) {
            $sid = self::get('user')['sid'];
        }

        return session_id() === $sid;
    }

    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }

        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = static::getValue($array, $keyPart);
            }
            $key = $lastKey;
        }

        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key   = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            // this is expected to fail if the property does not exist, or __get() is not implemented
            // it is not reliably possible to check whether a property is accessible beforehand
            return $array->$key;
        }elseif (is_array($array)) {
            return (isset($array[$key]) || array_key_exists($key, $array)) ? $array[$key] : $default;
        }

        return $default;
    }
}
