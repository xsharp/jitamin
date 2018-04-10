<table class="table-small table-scrolling">
    <tr>
        <th class="column-8"><?= $paginator->order(t('Id'), 'tasks.id') ?></th>
        <th class="column-8"><?= $paginator->order(t('Project'), 'tasks.project_id') ?></th>
        <th class="column-10"><?= $paginator->order(t('Swimlane'), 'tasks.swimlane_id') ?></th>
        <th class="column-10"><?= $paginator->order(t('Column'), 'tasks.column_id') ?></th>
        <th class="column-10"><?= $paginator->order(t('Category'), 'tasks.category_id') ?></th>
        <th><?= $paginator->order(t('Title'), 'tasks.title') ?></th>
        <th class="column-10"><?= $paginator->order(t('Assignee'), 'users.username') ?></th>
        <th class="column-10"><?= $paginator->order(t('Due date'), 'tasks.date_due') ?></th>
        <th class="column-8"><?= $paginator->order(t('Status'), 'tasks.is_active') ?></th>
    </tr>
    <?php foreach ($paginator->getCollection() as $task): ?>
    <tr>
        <td class="task-table color-<?= $task['color_id'] ?>">
            <?= $this->url->link('#'.$this->text->e($task['id']), 'Task/TaskController', 'show', ['task_id' => $task['id'], 'project_id' => $task['project_id']], false, '', t('View this task')) ?>
        </td>
        <td>
            <?= $this->url->link($this->text->e($task['project_name']), 'Project/Board/BoardController', 'show', ['project_id' => $task['project_id']]) ?>
        </td>
        <td>
            <?= $this->text->e($task['swimlane_name'] ?: $task['default_swimlane']) ?>
        </td>
        <td>
            <?= $this->text->e($task['column_name']) ?>
        </td>
        <td>
            <?= $this->text->e($task['category_name']) ?>
        </td>
        <td>
            <?= $this->url->link($this->text->e($task['title']), 'Task/TaskController', 'show', ['task_id' => $task['id'], 'project_id' => $task['project_id']], false, '', t('View this task')) ?>
        </td>
        <td>
            <?php if ($task['assignee_username']): ?>
                <?= $this->text->e($task['assignee_name'] ?: $task['assignee_username']) ?>
            <?php else: ?>
                <?= t('Unassigned') ?>
            <?php endif ?>
        </td>
        <td>
            <?= $this->dt->date($task['date_due']) ?>
        </td>
        <td>
            <?php if ($task['is_active'] == \Jitamin\Model\TaskModel::STATUS_OPEN): ?>
                <?= t('Open') ?>
            <?php else: ?>
                <?= t('Closed') ?>
            <?php endif ?>
        </td>
    </tr>
    <?php endforeach ?>
</table>

<div class="page-footer">
    <?= $paginator ?>
</div>
