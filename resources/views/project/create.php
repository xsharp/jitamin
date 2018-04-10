<div class="page-header">
    <h2><?= $title ?></h2>
</div>
<?php if ($is_private): ?>
<div class="alert alert-info">
    <p><?= t('There is no user management for private projects.') ?></p>
</div>
<?php endif ?>
<form class="popover-form" id="project-creation-form" method="post" action="<?= $this->url->href('Project/ProjectController', 'store') ?>" autocomplete="off">

    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('is_private', $values) ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, ['autofocus', 'required', 'maxlength="50"']) ?>

    <?php if (count($projects_list) > 1): ?>
        <?= $this->form->label(t('Create from another project'), 'src_project_id') ?>
        <?= $this->form->select('src_project_id', $projects_list, $values) ?>
    <?php endif ?>

    <div class="project-creation-options" <?= isset($values['src_project_id']) && $values['src_project_id'] > 0 ? '' : 'style="display: none"' ?>>
        <p class="alert"><?= t('Which parts of the project do you want to duplicate?') ?></p>

        <?php if (!$is_private): ?>
            <?= $this->form->checkbox('projectPermissionModel', t('Permissions'), 1, true) ?>
        <?php endif ?>

        <?= $this->form->checkbox('categoryModel', t('Categories'), 1, true) ?>
        <?= $this->form->checkbox('tagDuplicationModel', t('Tags'), 1, true) ?>
        <?= $this->form->checkbox('actionModel', t('Actions'), 1, true) ?>
        <?= $this->form->checkbox('swimlaneModel', t('Swimlanes'), 1, true) ?>
        <?= $this->form->checkbox('projectTaskDuplicationModel', t('Tasks'), 1, false) ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-success"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Dashboard/DashboardController', 'index', [], false, 'close-popover') ?>
    </div>
</form>
