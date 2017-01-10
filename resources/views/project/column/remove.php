<div class="page-header">
    <h2><?= t('Remove a column') ?></h2>
</div>

<form action="<?= $this->url->href('Project/Column/ColumnController', 'remove', ['project_id' => $project['id'], 'column_id' => $column['id']]) ?>" method="post" autocomplete="off">
    <?= $this->form->csrf() ?>
    <div class="confirm">
        <p class="alert alert-info">
            <?= t('Do you really want to remove this column: "%s"?', $column['title']) ?>
            <?= t('This action will REMOVE ALL TASKS associated to this column!') ?>
        </p>

        <div class="form-actions">
            <button type="submit" class="btn btn-danger"><?= t('Confirm') ?></button>
            <?= t('or') ?> <?= $this->url->link(t('cancel'), 'Project/Column/ColumnController', 'index', ['project_id' => $project['id']], false, 'close-popover') ?>
        </div>
    </div>
</form>
