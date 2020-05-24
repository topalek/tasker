<?php

namespace app\core;

class View
{
    public $layout = 'main';
    public $view = 'index';
    public $controller = 'site';

    public function __construct()
    {
    }

    public static function alert()
    {
        $alertTypes = [
            'error'   => 'alert-danger',
            'danger'  => 'alert-danger',
            'success' => 'alert-success',
            'info'    => 'alert-info',
            'warning' => 'alert-warning'
        ];
        $flash      = Session::get('flash');

        $html = '';
        if ( ! empty($flash)) {
            $type      = array_keys($flash)[0];
            $msg       = $flash[$type];
            $alertType = 'alert-info';
            if (array_key_exists($type, $alertTypes)) {
                $alertType = $alertTypes[$type];
            }
            $html = <<<JS
        <div class="alert $alertType alert-dismissible fade show" role="alert">
         $msg
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
                  <script>
        $(document).ready(function($) {
          $(".close").on("click",e=>{
          $(e.target).parents(".alert").remove();
        })
           
        })</script>
        </div>

JS;
            Session::delete('flash');
        }

        return $html;
    }

    public function render($view, $params, $controller)
    {
        if (is_array($params) && ! empty($params)) {
            extract($params);
        }
        $layout   = ($controller->layout) ?? $this->layout;
        $layout   = '../app/views/layouts/' . $layout;
        $viewFile = $controller->viewPath . $view;
        $content  = $this->renderPhpFile($viewFile, $params);
        $content  = $this->renderPhpFile($layout, ['content' => $content]);

        return $content;
    }

    private function renderPhpFile($view, $params)
    {
        if (is_array($params) && ! empty($params)) {
            extract($params);
        }
        ob_start();
        try {
            include $view . '.php';
        }catch (\Exception $e){
            var_dump($view);
        }

        return ob_get_clean();
    }

}
