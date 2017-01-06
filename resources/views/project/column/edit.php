<div class="page-header">
    <h2><?= t('Edit column "%s"', $column['title']) ?></h2>
</div>

<form class="popover-form" method="post" action="<?= $this->url->href('Project/Column/ColumnController', 'update', ['project_id' => $project['id'], 'column_id' => $column['id']]) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('id', $values) ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Title'), 'title') ?>
    <?= $this->form->text('title', $values, $errors, ['autofocus', 'required', 'maxlength="50"']) ?>

    <?= $this->form->label(t('Task limit'), 'task_limit') ?>
    <?= $this->form->number('task_limit', $values, $errors) ?>

    <?= $this->form->checkbox('hide_in_dashboard', t('Hide tasks in this column in the dashboard'), 1, $values['hide_in_dashboard'] == 1) ?>

    <?= $this->form->label(t('Description'), 'description') ?>
    <?= $this->form->textEditor('description', $values, $errors) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-info"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Project/Column/ColumnController', 'index', ['project_id' => $project['id']], false, 'close-popover') ?>
    </div>
</form>
