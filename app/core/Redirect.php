<?php

namespace app\core;


class Redirect
{
    public static function to($location = null)
    {
        if ($location) {
            header('Location: ' . $location);
            exit();
        }
    }
}