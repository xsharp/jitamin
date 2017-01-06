<div class="task-board color-<?= $task['color_id'] ?> <?= $task['date_modification'] > time() - $board_highlight_period ? 'task-board-recent' : '' ?>">

    <?= $this->url->link('#'.$task['id'], 'Task/TaskController', 'readonly', ['task_id' => $task['id'], 'token' => $project['token']]) ?>

    <?php if ($task['reference']): ?>
    <span class="task-board-reference" title="<?= t('Reference') ?>">
        (<?= $task['reference'] ?>)
    </span>
    <?php endif ?>

    <?= $this->render('project/board/task_avatar', ['task' => $task]) ?>

    <?= $this->hook->render('template:board:public:task:before-title', ['task' => $task]) ?>
    <div class="task-board-title">
        <?= $this->url->link($this->text->e($task['title']), 'Task/TaskController', 'readonly', ['task_id' => $task['id'], 'token' => $project['token']]) ?>
    </div>
    <?= $this->hook->render('template:board:public:task:after-title', ['task' => $task]) ?>

    <?= $this->render('project/board/task_footer', [
        'task'         => $task,
        'not_editable' => $not_editable,
        'project'      => $project,
    ]) ?>
</div>
