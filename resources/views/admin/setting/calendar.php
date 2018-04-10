<div class="page-header">
    <h2><?= t('Calendar settings') ?></h2>
</div>
<section>
<form method="post" action="<?= $this->url->href('Admin/SettingController', 'store', ['redirect' => 'calendar']) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <div class="listing">
        <h3><?= t('Project calendar view') ?></h3>
        <?= $this->form->radios('calendar_project_tasks', [
                'date_creation' => t('Show tasks based on the creation date'),
                'date_started'  => t('Show tasks based on the start date'),
            ], $values) ?>
    </div>

    <div class="listing">
        <h3><?= t('User calendar view') ?></h3>
        <?= $this->form->radios('calendar_user_tasks', [
                'date_creation' => t('Show tasks based on the creation date'),
                'date_started'  => t('Show tasks based on the start date'),
            ], $values) ?>
    </div>

    <div class="listing">
        <h3><?= t('Subtasks time tracking') ?></h3>
        <?= $this->form->checkbox('calendar_user_subtasks_time_tracking', t('Show subtasks based on the time tracking'), 1, $values['calendar_user_subtasks_time_tracking'] == 1) ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-success"><?= t('Save') ?></button>
    </div>
</form>
</section>
