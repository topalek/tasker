<?php
/**
 * Created by topalek
 * Date: 24.05.2020
 * Time: 20:08
 */

namespace app\controllers;


use app\core\Controller;
use app\core\Db;
use app\core\Input;
use app\core\Redirect;
use app\core\Security;
use app\core\Session;
use JasonGrimes\Paginator;

class Admin extends Controller
{
    public function index($page = 1)
    {
        $tasks = Db::getInstance()->get('task')->all();
        $totalItems = count($tasks);
        $itemsPerPage = 3;
        $urlPattern = '/site/index/(:num)';

        $tasks = Db::getInstance()->get('task')->limit(10)->offset($itemsPerPage * ($page - 1))->order(
            'id',
            'desc'
        )->all();
        $paginator = new Paginator($totalItems, $itemsPerPage, $page, $urlPattern);
        $this->render('index', ['tasks' => $tasks, 'paginator' => $paginator,]);
    }

    public function update($id)
    {
        $task = Db::getInstance()->get('task', ['id', '=', $id])->one();
        $oldText = $task->text;
        if (Input::exists()) {
            if (Security::checkCsrf(Input::post('_csrf'))) {
                $status = Input::post('status') ?? 0;
                $text   = Input::post('text');
                $edit   = 0;
                if ($oldText !== $text) {
                    $edit = 1;
                }
                $fields = ['status' => $status, 'text' => $text, 'edit' => $edit];
                var_dump(Db::getInstance()->update('task', $id, $fields));
                if (Db::getInstance()->update('task', $id, $fields)) {
                    Redirect::to('/admin');
                }else {
                    Session::flash('error', 'Возникла ошибка записи в БД. Попробуйте еще раз');
                }
            }
        }
        $this->render('update', ['task' => $task]);
    }

    protected function beforeAction()
    {
        parent::beforeAction();
        if ( ! Session::valid()) {
            Session::flash('warning', "У вас нет доступа");
            Redirect::to('/site/login');
        }
    }


}
