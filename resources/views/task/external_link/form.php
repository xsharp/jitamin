<?= $this->form->csrf() ?>
<?= $this->form->hidden('task_id', ['task_id' => $task['id']]) ?>
<?= $this->form->hidden('id', $values) ?>
<?= $this->form->hidden('link_type', $values) ?>

<?= $this->form->label(t('URL'), 'url') ?>
<?= $this->form->text('url', $values, $errors, ['required']) ?>

<?= $this->form->label(t('Title'), 'title') ?>
<?= $this->form->text('title', $values, $errors, ['required']) ?>

<?= $this->form->label(t('Dependency'), 'dependency') ?>
<?= $this->form->select('dependency', $dependencies, $values, $errors) ?>
