<?php
/**
 * @var View $this
 * @var Task $task
 */


use app\core\Security;
use app\core\View;
use app\models\Task;

?>

<div class="update">
    <form id="add" class="form" method="post">
        <ul class="list-group">
            <li class="list-group-item">
                <?= $task->user_name; ?>
                <span class="badge badge-secondary">Пользователь</span>
            </li>
            <li class="list-group-item">
                <?= $task->user_email; ?>
                <span class="badge badge-secondary">Email</span>
            </li>
            <li class="list-group-item">
                <div class="group">
                    <label for="status">Выполнено &nbsp;</label>
                    <input type="checkbox" name="status" id="status" <?= $task->status ? 'checked' : '' ?> value=1>
                </div>
                <button type="submit" class="btn btn-success">Обновить</button>
            </li>

        </ul>

        <input type="hidden" name="_csrf" value="<?= Security::generateCsrf() ?>">
        <div class="form-group text">
            <label for="text">Задача</label>
            <textarea class="form-control" name="text" id="text"
                      rows="7"><?= $task->text; ?></textarea>
        </div>
        <!--        <div class="form-group btn-wrapper">-->
        <!--            <input type="checkbox" value="--><?
        //= $task->status; ?><!--">-->
        <!--            <button type="submit" class="btn btn-success">Обновить</button>-->
        <!--        </div>-->

    </form>
</div>
