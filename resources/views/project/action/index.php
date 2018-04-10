<div class="page-header">
    <h2><?= t('Automatic actions for the project "%s"', $project['name']) ?></h2>
    <ul>
        <li>
            <i class="fa fa-plus fa-fw"></i>
            <?= $this->url->link(t('Add a new action'), 'Project/ActionController', 'create', ['project_id' => $project['id']], false, 'popover') ?>
        </li>
        <li>
            <i class="fa fa-copy fa-fw"></i>
            <?= $this->url->link(t('Import from another project'), 'Project/ProjectActionDuplicationController', 'show', ['project_id' => $project['id']], false, 'popover') ?>
        </li>
    </ul>
</div>

<?php if (empty($actions)): ?>
    <p class="alert"><?= t('There is no action at the moment.') ?></p>
<?php else: ?>
<table  class="actions-table table-striped"
        data-save-position-url="<?= $this->url->href('Project/ActionController', 'move', ['project_id' => $project['id']]) ?>">
        <thead>
        <tr>
            <th><?= t('ID') ?></th>
            <th><?= t('Automatic actions') ?></th>
            <th><?= t('Action parameters') ?></th>
            <th><?= t('Action') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($actions as $action): ?>
        <tr data-action-id="<?= $action['id'] ?>">
            <td>
                <i class="fa fa-arrows-alt draggable-row-handle" title="<?= t('Change category position') ?>"></i> <?= $action['id'] ?>
            </td>
            <td>
                <ul>
                    <li>

                        <?= t('Event name') ?> =
                        <strong><?= $this->text->in($action['event_name'], $available_events) ?></strong>
                    </li>
                    <li>
                        <?= t('Action name') ?> =
                        <strong><?= $this->text->in($action['action_name'], $available_actions) ?></strong>
                    </li>
                <ul>
            </td>
            <td>
                <ul>
                <?php foreach ($action['params'] as $param_name => $param_value): ?>
                    <li>
                        <?= $this->text->in($param_name, $available_params[$action['action_name']]) ?> =
                        <strong>
                        <?php if ($this->text->contains($param_name, 'column_id')): ?>
                            <?= $this->text->in($param_value, $columns_list) ?>
                        <?php elseif ($this->text->contains($param_name, 'user_id')): ?>
                            <?= $this->text->in($param_value, $users_list) ?>
                        <?php elseif ($this->text->contains($param_name, 'project_id')): ?>
                            <?= $this->text->in($param_value, $projects_list) ?>
                        <?php elseif ($this->text->contains($param_name, 'color_id')): ?>
                            <?= $this->text->in($param_value, $colors_list) ?>
                        <?php elseif ($this->text->contains($param_name, 'category_id')): ?>
                            <?= $this->text->in($param_value, $categories_list) ?>
                        <?php elseif ($this->text->contains($param_name, 'link_id')): ?>
                            <?= $this->text->in($param_value, $links_list) ?>
                        <?php elseif ($this->text->contains($param_name, 'swimlane_id')): ?>
                            <?= $this->text->in($param_value, $swimlane_list) ?>
                        <?php else: ?>
                            <?= $this->text->e($param_value) ?>
                        <?php endif ?>
                        </strong>
                    </li>
                <?php endforeach ?>
                </ul>
            </td>
            <td>
                <?= $this->url->link(t('Remove'), 'Project/ActionController', 'remove', ['project_id' => $project['id'], 'action_id' => $action['id']], false, 'popover') ?>
            </td>
        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>
