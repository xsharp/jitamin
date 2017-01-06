<?= $this->projectHeader->render($project, 'Task/TaskController', 'gantt') ?>
<div class="menu-inline">
    <ul>
        <li <?= $sorting === 'board' ? 'class="active"' : '' ?>>
            <i class="fa fa-sort-numeric-asc fa-fw"></i>
            <?= $this->url->link(t('Sort by position'), 'Task/TaskController', 'gantt', ['project_id' => $project['id'], 'sorting' => 'board']) ?>
        </li>
        <li <?= $sorting === 'date' ? 'class="active"' : '' ?>>
            <i class="fa fa-sort-amount-asc fa-fw"></i>
            <?= $this->url->link(t('Sort by date'), 'Task/TaskController', 'gantt', ['project_id' => $project['id'], 'sorting' => 'date']) ?>
        </li>
    </ul>
</div>

<?php if (!empty($tasks)): ?>
    <div
        id="gantt-chart"
        data-records='<?= json_encode($tasks, JSON_HEX_APOS) ?>'
        data-set-date-url="<?= $this->url->href('Task/TaskController', 'set_date', ['project_id' => $project['id']]) ?>"
        data-label-start-date="<?= t('Start date:') ?>"
        data-label-end-date="<?= t('Due date:') ?>"
        data-label-assignee="<?= t('Assignee:') ?>"
        data-label-not-defined="<?= t('There is no start date or due date for this task.') ?>"
    ></div>
    <p class="alert alert-info"><?= t('Moving or resizing a task will change the start and due date of the task.') ?></p>
<?php else: ?>
    <p class="alert"><?= t('There is no task in your project.') ?></p>
<?php endif ?>
