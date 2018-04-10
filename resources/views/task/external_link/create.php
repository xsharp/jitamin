<div class="page-header">
    <h2><?= t('Add a new external link') ?></h2>
</div>

<form class="popover-form" action="<?= $this->url->href('Task/TaskExternalLinkController', 'store', ['task_id' => $task['id'], 'project_id' => $task['project_id']]) ?>" method="post" autocomplete="off">
    <?= $this->render('task/external_link/form', ['task' => $task, 'dependencies' => $dependencies, 'values' => $values, 'errors' => $errors]) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-success"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Task/TaskExternalLinkController', 'show', ['task_id' => $task['id'], 'project_id' => $task['project_id']], false, 'close-popover') ?>
    </div>
</form>
