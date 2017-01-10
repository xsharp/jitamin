<div class="page-header">
    <h2><?= t('Remove a project restriction') ?></h2>
</div>

<form action="<?= $this->url->href('Project/ProjectRoleRestrictionController', 'remove', ['project_id' => $project['id'], 'restriction_id' => $restriction['restriction_id']]) ?>" method="post" autocomplete="off">
    <?= $this->form->csrf() ?>
    <div class="confirm">
        <p class="alert alert-info">
            <?= t('Do you really want to remove this project restriction: "%s"?', $this->text->in($restriction['rule'], $restrictions)) ?>
        </p>

        <div class="form-actions">
            <button type="submit" class="btn btn-danger"><?= t('Confirm') ?></button>
            <?= t('or') ?>
            <?= $this->url->link(t('cancel'), 'Project/ProjectRoleController', 'show', ['project_id' => $project['id']], false, 'close-popover') ?>
        </div>
    </div>
</form>
