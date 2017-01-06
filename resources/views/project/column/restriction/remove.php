<div class="page-header">
    <h2><?= t('Remove a column restriction') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to remove this column restriction?') ?>
    </p>

    <div class="form-actions">
        <?= $this->url->link(t('Confirm'), 'Project/Column/ColumnRestrictionController', 'remove', ['project_id' => $project['id'], 'restriction_id' => $restriction['restriction_id']], true, 'btn btn-danger') ?>
        <?= t('or') ?> <?= $this->url->link(t('cancel'), 'Project/ProjectRoleController', 'show', ['project_id' => $project['id']], false, 'close-popover') ?>
    </div>
</div>
