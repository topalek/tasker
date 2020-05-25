<?php
/**
 * @var View       $this
 * @var Task       $tasks
 * @var Paginator  $paginator
 * @var Validator  $validator
 * @var LinkSorter $linkSorter
 */

use app\core\LinkSorter;
use app\core\Security;
use app\core\Validator;
use app\core\View;
use app\models\Task;
use JasonGrimes\Paginator;


?>

<div id="todo">

    <form id="add" class="form" method="post">
        <div class="form-group user<?= $validator->getError('user') ? ' error' : '' ?>">
            <label for="name">Пользователь</label>
            <input type="text" name="user" id="user" value="<?= $validator->getValue('user'); ?>" required
                   class="form-control" placeholder="Petrovi4" aria-describedby="helpId">
            <div class="error-msg"><?= $validator->getError('user'); ?></div>
        </div>
        <div class="form-group email<?= $validator->getError('email') ? ' error' : '' ?>">
            <label for="email">Email</label>
            <input type="email"
                   name="email" id="email" required value="<?= $validator->getValue('email'); ?>" class="form-control"
                   placeholder="user@example.com" aria-describedby="helpId">
            <div class="error-msg"><?= $validator->getError('email'); ?></div>
        </div>
        <input type="hidden" name="_csrf" value="<?= Security::generateCsrf() ?>">
        <div class="form-group text<?= $validator->getError('text') ? ' error' : '' ?>">
            <label for="text">Задача</label>
            <textarea class="form-control" name="text" value="<?= $validator->getValue('text'); ?>" id="text"
                      rows="5"></textarea>
            <div class="error-msg"><?= $validator->getError('text'); ?></div>
        </div>
        <div class="form-group btn-wrapper">
            <button type="submit" class="btn btn-primary">Добавить задачу</button>
        </div>

    </form>
    <?php

    if ( ! empty($tasks)):?>
        <?= $linkSorter ?>
        <?php
        foreach ($tasks as $task) : ?>
            <div class="panel">
                <div class="panel-header">
                    <div class="id"><?= $task->id ?></div>
                    <div class="user-name"><?= $task->user_name ?></div>
                    <div class="email"><?= $task->user_email ?></div>
                    <div class="edit"><?= ($task->edit) ? '<span class="badge badge-info">отредактировано</span>' : "" ?></div>
                    <div class="status"><?= ($task->status) ? '<span class="badge badge-success">завершено</span>' : '<span class="badge badge-warning">активно</span>' ?></div>
                </div>
                <div class="panel-body">
                    <?= $task->text ?>
                </div>
            </div>

        <?php
        endforeach; ?>
    <?php
    else:; ?>
        <h2>Задач не найдено</h2>
    <?php
    endif; ?>

    <nav class="pages">
        <?php
        echo $paginator ?>
    </nav>

</div>
