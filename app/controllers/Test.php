<?php
/**
 * Created by topalek
 * Date: 22.05.2020
 * Time: 21:48
 */

namespace app\controllers;

use app\core\Controller;
use app\core\Security;
use app\Instagram\Instagram;

class Test extends Controller
{
    public function index()
    {
        $insta = new Instagram(
            [
                'apiKey'      => 'YOUR_APP_KEY',
                'apiSecret'   => 'YOUR_APP_SECRET',
                'apiCallback' => 'YOUR_APP_CALLBACK'
            ]
        );
        //$salt  = Security::salt(32);
        //$user->create(
        //    [
        //        'name'     => 'admin',
        //        'password' => Security::generatePassword('123', $salt),
        //        'salt'     => $salt
        //    ]
        //);
        print_r("done\n");
    }

}