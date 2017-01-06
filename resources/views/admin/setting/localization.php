<div class="page-header">
    <h2><?= t('Localization settings') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('Admin/SettingController', 'store', ['redirect' => 'localization']) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

   

    <?= $this->form->label(t('Language'), 'application_language') ?>
    <?= $this->form->select('application_language', $languages, $values, $errors) ?>

    <?= $this->form->label(t('Timezone'), 'application_timezone') ?>
    <?= $this->form->select('application_timezone', $timezones, $values, $errors) ?>

    <?= $this->form->label(t('Date format'), 'application_date_format') ?>
    <?= $this->form->select('application_date_format', $date_formats, $values, $errors) ?>
    <p class="form-help"><?= t('ISO format is always accepted, example: "%s" and "%s"', date('Y-m-d'), date('Y_m_d')) ?></p>

    <?= $this->form->label(t('Date and time format'), 'application_datetime_format') ?>
    <?= $this->form->select('application_datetime_format', $datetime_formats, $values, $errors) ?>

    <?= $this->form->label(t('Time format'), 'application_time_format') ?>
    <?= $this->form->select('application_time_format', $time_formats, $values, $errors) ?>

    <?= $this->hook->render('template:admin:setting:localization', ['values' => $values, 'errors' => $errors]) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-info"><?= t('Save') ?></button>
    </div>
</form>
