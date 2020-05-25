<?php


namespace app\controllers;

use app\core\Controller;
use app\core\Csrf;
use app\core\Db;
use app\core\Input;
use app\core\LinkSorter;
use app\core\Post;
use app\core\Redirect;
use app\core\Security;
use app\core\Session;
use app\core\User;
use app\core\Validator;
use app\models\Task;
use JasonGrimes\Paginator;

class Site extends Controller
{
    public function index($page = 1)
    {
        $validator = new Validator([]);

        $sort      = Input::get('sort');
        $sortField = $order = null;
        $tasks     = Db::getInstance()->get('task')->all();
        if (Input::exists()) {
            $validator = new Validator($_POST);
            $validator->name('email')->required('Заполните поле')->email("Введите правильный email адрес");
            $validator->name('user')->required('Заполните поле')->minSize(2, "Введите более 2х символов");
            $validator->name('text')->required('Заполните поле');
            if (Security::checkCsrf(Input::post('_csrf')) && $validator->isValid()) {
                $task             = new Task();
                $task->user_name  = Db::escape(Input::post('user'));
                $task->user_email = Db::escape(Input::post('email'));
                $task->text       = Db::escape(Input::post('text'));
                if ($task->save()) {
                    Session::flash('success', "Задание добавлено");
                    Redirect::to('/');
                }
            }
        }
        $totalItems   = count($tasks);
        $itemsPerPage = 3;
        $urlPattern   = '/site/index/(:num)';

        $tasks = Db::getInstance()->get('task')->limit(3)->offset($itemsPerPage * ($page - 1))->order(
            'id',
            'desc'
        )->all();


        if ($sort && strpos($sort, '-') !== false) {
            [$sortField, $order] = explode("-", $sort);
            $sortFields = ['user' => 'user_name', 'email' => 'user_email', 'status' => 'status'];
            if (array_key_exists($sortField, $sortFields) && in_array($order, ['desc', 'asc'])) {
                $urlPattern = '/site/index/(:num)?sort=' . "{$sortField}-$order";

                $tasks = Db::getInstance()->get('task')->limit(3)->offset($itemsPerPage * ($page - 1))->order(
                    $sortFields[$sortField],
                    $order
                )->all();
            }
        }
        $linkSorter = new LinkSorter($sortField, $order);
        $paginator  = new Paginator($totalItems, $itemsPerPage, $page, $urlPattern);

        $this->render(
            'index',
            [
                'tasks'      => $tasks,
                'paginator'  => $paginator,
                'validator'  => $validator,
                'linkSorter' => $linkSorter
            ]
        );
    }

    public function error()
    {
        $this->render('404');
    }

    public function login()
    {
        $validator = new Validator([]);

        if (Input::exists()) {
            $validator = new Validator(Input::post());
            $validator->name('pass')->required('Заполните поле');
            $validator->name('user')->required('Заполните поле');

            if (Security::checkCsrf(Input::post('_csrf')) && $validator->isValid()) {
                $name = Input::post('user');
                $pass = Input::post('pass');

                $user = new User();
                $login = $user->login($name, $pass);
                if ($login) {
                    Redirect::to('/admin');
                } else {
                    Session::flash('warning', 'Введены неверные логин или пароль');
                    Redirect::to('/site/login');
                }
            } else {
                var_dump([Security::checkCsrf(Input::post('_csrf')), Input::post('_csrf')]);
            }
        }

        $this->render('login', ['validator' => $validator]);
    }

    public function logout()
    {
        session_start();
        session_destroy();
        Redirect::to('/site/index');
    }
}
