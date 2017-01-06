<div class="page-header">
    <h2><?= t('New drag and drop restriction for the role "%s"', $role['role']) ?></h2>
</div>
<form class="popover-form" method="post" action="<?= $this->url->href('Project/Column/ColumnMoveRestrictionController', 'store', ['project_id' => $project['id']]) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('project_id', $values) ?>
    <?= $this->form->hidden('role_id', $values) ?>

    <?= $this->form->label(t('Source column'), 'src_column_id') ?>
    <?= $this->form->select('src_column_id', $columns, $values, $errors) ?>

    <?= $this->form->label(t('Destination column'), 'dst_column_id') ?>
    <?= $this->form->select('dst_column_id', $columns, $values, $errors) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-info"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Project/ProjectRoleController', 'show', [], false, 'close-popover') ?>
    </div>

    <p class="alert alert-info"><?= t('People belonging to this role will be able to move tasks only between the source and the destination column.') ?></p>
</form>
