<div class="page-header">
    <h2><?= t('Change default swimlane') ?></h2>
</div>
<form class="popover-form" method="post" action="<?= $this->url->href('Project/SwimlaneController', 'updateDefault', ['project_id' => $project['id']]) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('id', $values) ?>

    <?= $this->form->label(t('Name'), 'default_swimlane') ?>
    <?= $this->form->text('default_swimlane', $values, $errors, ['required', 'maxlength="50"']) ?>

    <?= $this->form->checkbox('show_default_swimlane', t('Show default swimlane'), 1, $values['show_default_swimlane'] == 1) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-info"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Project/SwimlaneController', 'index', ['project_id' => $project['id']], false, 'close-popover') ?>
    </div>
</form>
