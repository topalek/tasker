<?php

namespace app\core;

class Controller
{
    public $controllerName;
    public $viewPath;
    public $layout;
    private $_view;

    public function __construct()
    {
        $this->controllerName = explode("\\", get_class($this));
        $this->controllerName = strtolower(end($this->controllerName));
        $this->viewPath       = '../app/views/' . $this->controllerName . '/';
        $this->_view = new View();
        $this->beforeAction();
    }

    public function getModel($model)
    {
        require_once '../app/models/' . $model . '.php';

        return new $model;
    }

    public function render($view, $params = [])
    {
        echo $this->getView()->render($view, $params, $this);
    }

    public function getView()
    {
        return $this->_view;
    }

    protected function beforeAction()
    {
        Security::generateCsrf();
    }
}
