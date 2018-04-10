<?php echo $this->render('admin/_partials/update', ['is_outdated' => $is_outdated, 'current_version' => $current_version, 'latest_version' => $latest_version]) ?>
<div class="page-header">
    <h2><?= t('About') ?></h2>
</div>
<div class="listing">
    <ul>
        <li>
            <?= t('Official website:') ?>
            <a href="http://jitamin.com/" target="_blank" rel="noreferer">jitamin.com</a>
        </li>
        <li>
            <?= t('License:') ?>
            <strong>MIT</strong>
        </li>
    </ul>
</div>

<div class="page-header">
    <h2><?= t('Configuration') ?></h2>
</div>
<div class="listing">
    <ul>
        <li>
            <?= t('Application version:') ?>
            <strong><?= APP_VERSION ?></strong>
        </li>
        <li>
            <?= t('PHP version:') ?>
            <strong><?= PHP_VERSION ?></strong>
        </li>
        <li>
            <?= t('PHP SAPI:') ?>
            <strong><?= PHP_SAPI ?></strong>
        </li>
        <li>
            <?= t('OS version:') ?>
            <strong><?= php_uname('s').' '.php_uname('r') ?></strong>
        </li>
        <li>
            <?= t('Database driver:') ?>
            <strong><?= DB_DRIVER ?></strong>
        </li>
        <li>
            <?= t('Database version:') ?>
            <strong><?= $this->text->e($db_version) ?></strong>
        </li>
        <li>
            <?= t('Browser:') ?>
            <strong><?= $this->text->e($user_agent) ?></strong>
        </li>
    </ul>
</div>

<?php if (DB_DRIVER === 'sqlite'): ?>
    <div class="page-header">
        <h2><?= t('Database') ?></h2>
    </div>
    <div class="listing">
        <ul>
            <li>
                <?= t('Database size:') ?>
                <strong><?= $this->text->bytes($db_size) ?></strong>
            </li>
            <li>
                <?= $this->url->link(t('Download the database'), 'Admin/SettingController', 'downloadDb', [], true) ?>&nbsp;
                <?= t('(Gzip compressed Sqlite file)') ?>
            </li>
            <li>
                <?= $this->url->link(t('Optimize the database'), 'Admin/SettingController', 'optimizeDb', [], true) ?>&nbsp;
                <?= t('(VACUUM command)') ?>
            </li>
        </ul>
    </div>
<?php endif ?>

<div class="page-header">
    <h2><?= t('License') ?></h2>
</div>
<div class="listing">
<?= nl2br(file_get_contents(JITAMIN_DIR.'/LICENSE')) ?>
</div>
