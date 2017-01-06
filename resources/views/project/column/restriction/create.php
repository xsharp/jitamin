<div class="page-header">
    <h2><?= t('New column restriction for the role "%s"', $role['role']) ?></h2>
</div>
<form class="popover-form" method="post" action="<?= $this->url->href('Project/Column/ColumnRestrictionController', 'store', ['project_id' => $project['id']]) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('project_id', $values) ?>
    <?= $this->form->hidden('role_id', $values) ?>

    <?= $this->form->label(t('Rule'), 'rule') ?>
    <?= $this->form->select('rule', $rules, $values, $errors) ?>

    <?= $this->form->label(t('Column'), 'column_id') ?>
    <?= $this->form->select('column_id', $columns, $values, $errors) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-info"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Project/ProjectRoleController', 'show', [], false, 'close-popover') ?>
    </div>
</form>
