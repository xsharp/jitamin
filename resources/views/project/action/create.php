<div class="page-header">
    <h2><?= t('Add an action') ?></h2>
</div>
<form class="popover-form" method="post" action="<?= $this->url->href('Project/ActionController', 'event', ['project_id' => $project['id']]) ?>">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Action'), 'action_name') ?>
    <?= $this->form->select('action_name', $available_actions, $values) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-success"><?= t('Next step') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Project/ActionController', 'index', [], false, 'close-popover') ?>
    </div>
</form>
