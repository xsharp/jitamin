<div class="page-header">
    <h2><?= t('Disable two factor authentication') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info">
        <?= t('Do you really want to disable the two factor authentication for this user: "%s"?', $user['name'] ?: $user['username']) ?>
    </p>

    <div class="form-actions">
        <?= $this->url->link(t('Confirm'), 'Profile/TwoFactorController', 'disable', ['user_id' => $user['id'], 'disable' => 'yes'], true, 'btn btn-danger') ?>
        <?= t('or') ?> <?= $this->url->link(t('cancel'), 'Profile/ProfileController', 'show', ['user_id' => $user['id']]) ?>
    </div>
</div>
