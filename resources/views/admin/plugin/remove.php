<div class="page-header">
    <h2><?= t('Remove plugin') ?></h2>
</div>

<div class="confirm">
    <p class="alert alert-info"><?= t('Do you really want to remove this plugin: "%s"?', $plugin->getPluginName()) ?></p>

    <div class="form-actions">
        <?= $this->url->link(t('Confirm'), 'Admin/PluginController', 'uninstall', ['pluginId' => $plugin_id], true, 'btn btn-danger') ?>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Admin/PluginController', 'show', [], false, 'close-popover') ?>
    </div>
</div>
