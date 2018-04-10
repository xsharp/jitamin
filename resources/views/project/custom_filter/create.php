<div class="page-header">
    <h2><?= t('Add a custom filter') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('Project/CustomFilterController', 'store', ['project_id' => $project['id']]) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, ['required'], 'maxlength="100"') ?>

    <?= $this->form->label(t('Filter'), 'filter') ?>
    <?= $this->form->text('filter', $values, $errors, ['required', 'maxlength="100"']) ?>

    <?php if ($this->user->hasProjectAccess('Manage/ProjectSettingsController', 'edit', $project['id'])): ?>
        <?= $this->form->checkbox('is_shared', t('Share with all project members'), 1) ?>
    <?php endif ?>

    <?= $this->form->checkbox('append', t('Append filter (instead of replacement)'), 1) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-success"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Project/CustomFilterController', 'index', ['project_id' => $project['id']], false, 'close-popover') ?>
    </div>
</form>
