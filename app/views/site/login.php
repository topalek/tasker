<?php

use app\core\Security;
use app\core\Validator;
use app\core\View;

/**
 *
 * @var View      $this
 * @var Validator $validator
 */
?>
<div id="login">
    <form method="post">
        <h2>Панель редактирования</h2>

        <div class="form-group<?= $validator->getError('user') ? ' error' : '' ?>">
            <label for="user">Login</label>
            <input type="text" name="user" autocomplete="off" class="form-control" required id="user"
                   value="<?= $validator->getValue('user'); ?>">
            <div class="error-msg"><?= $validator->getError('user'); ?></div>
        </div>
        <div class="form-group<?= $validator->getError('pass') ? ' error' : '' ?>">
            <label for="pass">Password</label>
            <input type="password" name="pass" required class="form-control" id="pass">
            <div class="error-msg"><?= $validator->getError('pass'); ?></div>
        </div>
        <input type="hidden" name="_csrf" value="<?= Security::generateCsrf() ?>">

        <button type="submit" class="btn btn-primary">Вход</button>
    </form>
</div>
