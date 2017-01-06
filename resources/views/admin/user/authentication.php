<div class="page-header">
    <h2><?= t('Edit Authentication') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('Admin/UserController', 'saveAuthentication', ['user_id' => $user['id']]) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('id', $values) ?>
    <?= $this->form->hidden('username', $values) ?>

    <?= $this->hook->render('template:user:authentication:form', ['values' => $values, 'errors' => $errors, 'user' => $user]) ?>

    <?= $this->form->checkbox('is_ldap_user', t('Remote user'), 1, isset($values['is_ldap_user']) && $values['is_ldap_user'] == 1) ?>
    <?= $this->form->checkbox('disable_login_form', t('Disallow login form'), 1, isset($values['disable_login_form']) && $values['disable_login_form'] == 1) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-info"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Profile/ProfileController', 'show', ['user_id' => $user['id']]) ?>
    </div>

    <div class="alert alert-info">
        <ul>
            <li><?= t('Remote users do not store their password in Jitamin database, examples: LDAP, Google and Github accounts.') ?></li>
            <li><?= t('If you check the box "Disallow login form", credentials entered in the login form will be ignored.') ?></li>
        </ul>
    </div>
</form>
