<?php

namespace app\core;


class App
{
    protected $controller = 'app\controllers\Site';
    protected $action = 'index';
    protected $params = [];
    protected $queryParams = [];
    protected $db;

    public function __construct()
    {
        session_start();
        $url = $this->parseUrl();

        if ( ! empty($url)) {
            if (class_exists('app\controllers\\' . ucfirst($url[0]))) {
                $this->controller = 'app\controllers\\' . ucfirst($url[0]);
                unset($url[0]);
            }
        }

        $this->controller = new $this->controller;

        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->action = $url[1];
                unset($url[1]);
            }
        }
        $this->params = $url ? array_values($url) : [];
        call_user_func_array([$this->controller, $this->action], $this->params);
    }

    public function parseUrl()
    {
        $url = [];

        if (isset($_GET['url'])) {
            $url = explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
            unset($_GET['url']);
        }

        return $url;
    }
}