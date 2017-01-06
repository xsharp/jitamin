<div class="page-header">
    <h2><?= t('New remote user') ?></h2>
</div>
<form class="popover-form" method="post" action="<?= $this->url->href('Admin/UserController', 'store') ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('is_ldap_user', ['is_ldap_user' => 1]) ?>

    <div class="form-columns">
        <div class="form-column">
            <?= $this->form->label(t('Username'), 'username') ?>
            <?= $this->form->text('username', $values, $errors, ['autofocus', 'required', 'maxlength="50"']) ?>
    
            <?= $this->form->label(t('Name'), 'name') ?>
            <?= $this->form->text('name', $values, $errors) ?>

            <?= $this->form->label(t('Email'), 'email') ?>
            <?= $this->form->email('email', $values, $errors, ['required']) ?>

            <?= $this->hook->render('template:user:create-remote:form', ['values' => $values, 'errors' => $errors]) ?>
        </div>

        <div class="form-column">
            <?= $this->form->label(t('Add project member'), 'project_id') ?>
            <?= $this->form->select('project_id', $projects, $values, $errors) ?>

            <?= $this->form->label(t('Timezone'), 'timezone') ?>
            <?= $this->form->select('timezone', $timezones, $values, $errors) ?>

            <?= $this->form->label(t('Language'), 'language') ?>
            <?= $this->form->select('language', $languages, $values, $errors) ?>

            <?= $this->form->label(t('Role'), 'role') ?>
            <?= $this->form->select('role', $roles, $values, $errors) ?>

            <?= $this->form->checkbox('notifications_enabled', t('Enable email notifications'), 1, isset($values['notifications_enabled']) && $values['notifications_enabled'] == 1 ? true : false) ?>
            <?= $this->form->checkbox('disable_login_form', t('Disallow login form'), 1, isset($values['disable_login_form']) && $values['disable_login_form'] == 1) ?>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-info"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Admin/UserController', 'index', [], false, 'close-popover') ?>
    </div>
</form>
<div class="alert alert-info">
    <ul>
        <li><?= t('Remote users do not store their password in Jitamin database, examples: LDAP, Google and Github accounts.') ?></li>
        <li><?= t('If you check the box "Disallow login form", credentials entered in the login form will be ignored.') ?></li>
    </ul>
</div>
