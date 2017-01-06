<div class="page-header">
    <h2><?= t('Add a new external link') ?></h2>
</div>

<form class="popover-form" action="<?= $this->url->href('Task/TaskExternalLinkController', 'create', ['task_id' => $task['id'], 'project_id' => $task['project_id']]) ?>" method="post" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('task_id', ['task_id' => $task['id']]) ?>

    <?= $this->form->label(t('External link'), 'text') ?>
    <?= $this->form->text(
        'text',
        $values,
        $errors,
        [
            'required',
            'autofocus',
            'placeholder="'.t('Copy and paste your link here...').'"',
        ]) ?>

    <?= $this->form->label(t('Link type'), 'type') ?>
    <?= $this->form->select('type', $types, $values) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-info"><?= t('Next') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Task/TaskController', 'show', ['task_id' => $task['id'], 'project_id' => $task['project_id']], false, 'close-popover') ?>
    </div>
</form>
