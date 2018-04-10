<div class="page-header">
    <h2><?= t('Edit a tag') ?></h2>
</div>
<form method="post" class="popover-form" action="<?= $this->url->href('Manage/ProjectTagController', 'update', ['tag_id' => $tag['id'], 'project_id' => $project['id']]) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('id', $values) ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, ['autofocus', 'required', 'maxlength="255"']) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-success"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Manage/ProjectTagController', 'index', [], false, 'close-popover') ?>
    </div>
</form>
