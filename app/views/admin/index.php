<?php
/**
 * @var View      $this
 * @var Task      $tasks
 * @var Paginator $paginator
 */


use app\core\View;
use app\models\Task;
use JasonGrimes\Paginator;

?>
<div id="todo" class="admin">
    <?php
    foreach ($tasks as $task) :?>
        <div class="panel">
            <div class="panel-header">
                <div class="id"><?= $task->id ?></div>
                <div class="user-name"><?= $task->user_name ?></div>
                <div class="email"><?= $task->user_email ?></div>
                <div class="edit"><?= ($task->edit) ? 'отредактировано' : "" ?></div>
                <div class="status">
                    <div class="status"><?= ($task->status) ? '<span class="badge badge-success">завершено</span>' : '<span class="badge badge-warning">активно</span>' ?></div>
                </div>
                <a href="/admin/update/<?= $task->id ?>" title="редактировать" class="update-link"><span
                            class="badge badge-primary">редактировать</span></a>
            </div>
            <div class="panel-body">
                <?= $task->text ?>
            </div>
        </div>
    <?php
    endforeach; ?>
    <nav class="pages">
        <?php
        echo $paginator ?>
    </nav>
</div>
