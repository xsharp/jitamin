<div class="page-header">
    <h2><?= t('Define action parameters') ?></h2>
</div>

<form class="popover-form" method="post" action="<?= $this->url->href('Project/ActionController', 'store', ['project_id' => $project['id']]) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('project_id', $values) ?>
    <?= $this->form->hidden('event_name', $values) ?>
    <?= $this->form->hidden('action_name', $values) ?>

    <?= $this->form->label(t('Action'), 'action_name') ?>
    <?= $this->form->select('action_name', $available_actions, $values, [], ['disabled']) ?>

    <?= $this->form->label(t('Event'), 'event_name') ?>
    <?= $this->form->select('event_name', $events, $values, [], ['disabled']) ?>

    <?php foreach ($action_params as $param_name => $param_desc): ?>
        <?php if ($this->text->contains($param_name, 'column_id')): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->select('params['.$param_name.']', $columns_list, $values) ?>
        <?php elseif ($this->text->contains($param_name, 'comparison')): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->select('params['.$param_name.']', $comparisons_list, $values) ?>
        <?php elseif ($this->text->contains($param_name, 'user_id')): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->select('params['.$param_name.']', $users_list, $values) ?>
        <?php elseif ($this->text->contains($param_name, 'project_id')): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->select('params['.$param_name.']', $projects_list, $values) ?>
        <?php elseif ($this->text->contains($param_name, 'color_id')): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->select('params['.$param_name.']', $colors_list, $values) ?>
        <?php elseif ($this->text->contains($param_name, 'category_id')): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->select('params['.$param_name.']', $categories_list, $values) ?>
        <?php elseif ($this->text->contains($param_name, 'link_id')): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->select('params['.$param_name.']', $links_list, $values) ?>
        <?php elseif ($param_name === 'priority'): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->select('params['.$param_name.']', $priorities_list, $values) ?>
        <?php elseif ($this->text->contains($param_name, 'duration')): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->number('params['.$param_name.']', $values) ?>
        <?php elseif ($this->text->contains($param_name, 'swimlane_id')): ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->select('params['.$param_name.']', $swimlane_list, $values) ?>
        <?php else: ?>
            <?= $this->form->label($param_desc, $param_name) ?>
            <?= $this->form->text('params['.$param_name.']', $values) ?>
        <?php endif ?>
    <?php endforeach ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-success"><?= t('Save') ?></button>
        <?= t('or') ?>
        <?= $this->url->link(t('cancel'), 'Project/ActionController', 'index', ['project_id' => $project['id']], false, 'close-popover') ?>
    </div>
</form>
