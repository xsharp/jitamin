<div class="
        task-board
        <?= $task['is_draggable'] ? 'draggable-item ' : '' ?>
        <?= $task['is_active'] == 1 ? 'task-board-status-open '.($task['date_modification'] > (time() - $board_highlight_period) ? 'task-board-recent' : '') : 'task-board-status-closed' ?>
        color-<?= $task['color_id'] ?>"
     data-task-id="<?= $task['id'] ?>"
     data-column-id="<?= $task['column_id'] ?>"
     data-swimlane-id="<?= $task['swimlane_id'] ?>"
     data-position="<?= $task['position'] ?>"
     data-owner-id="<?= $task['owner_id'] ?>"
     data-category-id="<?= $task['category_id'] ?>"
     data-due-date="<?= $task['date_due'] ?>"
     data-task-url="<?= $this->url->href('Task/TaskController', 'show', ['task_id' => $task['id'], 'project_id' => $task['project_id']]) ?>">

    <div class="task-board-sort-handle" style="display: none;"><i class="fa fa-arrows-alt"></i></div>

    <?php if ($this->board->isCollapsed($task['project_id'])): ?>
        <div class="task-board-collapsed">
            <div class="task-board-saving-icon" style="display: none;"><i class="fa fa-spinner fa-pulse"></i></div>
            <?php if ($this->user->hasProjectAccess('Task/TaskController', 'edit', $task['project_id'])): ?>
                <?= $this->render('task/dropdown', ['task' => $task]) ?>
            <?php else: ?>
                <strong><?= '#'.$task['id'] ?></strong>
            <?php endif ?>

            <?php if (!empty($task['assignee_username'])): ?>
                <span title="<?= $this->text->e($task['assignee_name'] ?: $task['assignee_username']) ?>">
                    <?= $this->text->e($this->user->getInitials($task['assignee_name'] ?: $task['assignee_username'])) ?>
                </span> -
            <?php endif ?>
            <?= $this->url->link($this->text->e($task['title']), 'Task/TaskController', 'show', ['task_id' => $task['id'], 'project_id' => $task['project_id']], false, 'task-board-collapsed-title tooltip', $this->text->e($task['title'])) ?>
        </div>
    <?php else: ?>
        <div class="task-board-expanded">
            <div class="task-board-saving-icon" style="display: none;"><i class="fa fa-spinner fa-pulse fa-2x"></i></div>
            <?php if ($this->user->hasProjectAccess('Task/TaskController', 'edit', $task['project_id'])): ?>
                <?= $this->render('task/dropdown', ['task' => $task]) ?>
            <?php else: ?>
                <strong><?= '#'.$task['id'] ?></strong>
            <?php endif ?>

            <?php if ($task['reference']): ?>
            <span class="task-board-reference" title="<?= t('Reference') ?>">
                (<?= $task['reference'] ?>)
            </span>
            <?php endif ?>

            <?= $this->render('project/board/task_avatar', ['task' => $task]) ?>

            <?= $this->hook->render('template:board:private:task:before-title', ['task' => $task]) ?>
            <div class="task-board-title">
                <?= $this->url->link($this->text->e($task['title']), 'Task/TaskController', 'show', ['task_id' => $task['id'], 'project_id' => $task['project_id']], false, '', t('View this task')) ?>
            </div>
            <?= $this->hook->render('template:board:private:task:after-title', ['task' => $task]) ?>

            <?= $this->render('project/board/task_footer', [
                'task'         => $task,
                'not_editable' => $not_editable,
                'project'      => $project,
            ]) ?>
        </div>
    <?php endif ?>
</div>
