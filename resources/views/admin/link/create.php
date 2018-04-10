<div class="page-header">
    <h2><?= t('Add a new link') ?></h2>
</div>

<form action="<?= $this->url->href('Admin/LinkController', 'store') ?>" method="post" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->label(t('Label'), 'label') ?>
    <?= $this->form->text('label', $values, $errors, ['required']) ?>

    <?= $this->form->label(t('Opposite label'), 'opposite_label') ?>
    <?= $this->form->text('opposite_label', $values, $errors) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-success"><?= t('Save') ?></button>
    </div>
</form>
