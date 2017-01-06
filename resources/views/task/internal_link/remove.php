<div class="page-header">
    <h2><?= t('Remove a link') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this link with task #%d?', $link['opposite_task_id']) ?>
    </p>

    <div class="form-actions">
        <?= $this->url->link(t('Confirm'), 'Task/TaskInternalLinkController', 'remove', ['link_id' => $link['id'], 'task_id' => $task['id'], 'project_id' => $task['project_id']], true, 'btn btn-danger') ?>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Task/TaskController', 'show', ['task_id' => $task['id'], 'project_id' => $task['project_id']], false, 'close-popover') ?>
    </div>
</div>
