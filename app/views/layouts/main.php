<?php
/**
 * @var View   $this
 * @var string $content
 */

use app\core\Session;
use app\core\User;
use app\core\View;

?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Todo</title>
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/jquery.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark d-flex justify-content-between">
    <div class="container">
        <div class="row">
            <div class="col  d-flex justify-content-between">
                <a class="navbar-brand" href="/">Tasker</a>
                <?php
                if (Session::valid()):
                    $user = (new User)->find(Session::get('user')['id'])
                    ?>
                    <div class="admin">
                        <a class="login-link btn btn-info" href="/site/logout">Logout (<?= $user->data()->name ?>)</a>
                        <a class="login-link btn btn-info" href="/admin">Admin panel</a>
                    </div>

                <?php
                else: ?>
                    <a class="login-link  btn btn-info" href="/site/login">Login</a>
                <?php
                endif; ?>
            </div>
        </div>
    </div>
</nav>
<div class="container">
    <div class="row">
        <div class="col">
            <?= View::alert() ?>
            <?= $content ?>
        </div>
    </div>
</div>
</body>
</html>
