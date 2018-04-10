<div class="page-header">
    <h2><?= t('Add a new column') ?></h2>
</div>
<form class="popover-form" method="post" action="<?= $this->url->href('Project/Column/ColumnController', 'store', ['project_id' => $project['id']]) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Title'), 'title') ?>
    <?= $this->form->text('title', $values, $errors, ['autofocus', 'required', 'maxlength="50"']) ?>

    <?= $this->form->label(t('Task limit'), 'task_limit') ?>
    <?= $this->form->number('task_limit', $values, $errors) ?>

    <?= $this->form->checkbox('hide_in_dashboard', t('Hide tasks in this column in the dashboard'), 1) ?>

    <?= $this->form->label(t('Description'), 'description') ?>
    <?= $this->form->textEditor('description', $values, $errors) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-success"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Project/Column/ColumnController', 'index', ['project_id' => $project['id']], false, 'close-popover') ?>
    </div>
</form>
