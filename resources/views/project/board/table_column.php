<!-- column titles -->
<tr class="board-swimlane-columns-<?= $swimlane['id'] ?>">
    <?php foreach ($swimlane['columns'] as $column): ?>
    <th class="board-column-header board-column-header-<?= $column['id'] ?>" data-column-id="<?= $column['id'] ?>">

        <!-- column in collapsed mode -->
        <div class="board-column-collapsed">
            <small class="board-column-header-task-count" title="<?= t('Show this column') ?>">
                <span id="task-number-column-<?= $column['id'] ?>"><?= $column['nb_tasks'] ?></span>
            </small>
        </div>

        <!-- column in expanded mode -->
        <div class="board-column-expanded">
            <?php if (!$not_editable && $this->projectRole->canCreateTaskInColumn($column['project_id'], $column['id'])): ?>
                <div class="board-add-icon">
                    <?= $this->url->link('+', 'Task/TaskController', 'create', ['project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']], false, 'popover large', t('Add a new task')) ?>
                </div>
            <?php endif ?>

            <?php if ($swimlane['nb_swimlanes'] > 1 && !empty($column['column_nb_tasks'])): ?>
                <span title="<?= t('Total number of tasks in this column across all swimlanes') ?>" class="board-column-header-task-count">
                    (<span><?= $column['column_nb_tasks'] ?></span>)
                </span>
            <?php endif ?>

            <span class="board-column-title">
                <?php if ($not_editable): ?>
                    <?= $this->text->e($column['title']) ?>
                <?php else: ?>
                    <span class="dropdown">
                        <a href="#" class="dropdown-menu"><?= $this->text->e($column['title']) ?> <i class="fa fa-caret-down"></i></a>
                        <ul>
                            <li>
                                <i class="fa fa-minus-square fa-fw"></i>
                                <a href="#" class="board-toggle-column-view" data-column-id="<?= $column['id'] ?>"><?= t('Hide this column') ?></a>
                            </li>
                            <?php if ($this->projectRole->canCreateTaskInColumn($column['project_id'], $column['id'])): ?>
                                <li>
                                    <i class="fa fa-align-justify fa-fw" aria-hidden="true"></i>
                                    <?= $this->url->link(t('Create tasks in bulk'), 'Task/TaskBulkController', 'show', ['project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']], false, 'popover') ?>
                                </li>
                            <?php endif ?>

                            <?php if ($column['nb_tasks'] > 0 && $this->projectRole->canChangeTaskStatusInColumn($column['project_id'], $column['id'])): ?>
                                <li>
                                    <i class="fa fa-close fa-fw"></i>
                                    <?= $this->url->link(t('Close all tasks of this column'), 'Project/Board/BoardPopoverController', 'closeColumnTasks', ['project_id' => $column['project_id'], 'column_id' => $column['id'], 'swimlane_id' => $swimlane['id']], false, 'popover') ?>
                                </li>
                            <?php endif ?>

                            <?= $this->hook->render('template:board:column:dropdown', ['swimlane' => $swimlane, 'column' => $column]) ?>
                        </ul>
                    </span>
                <?php endif ?>
            </span>

            <?php if (!$not_editable && !empty($column['description'])): ?>
                <span class="tooltip text-right" title="<?= $this->text->markdownAttribute($column['description']) ?>">
                    &nbsp;<i class="fa fa-info-circle"></i>
                </span>
            <?php endif ?>

            <?php if (!empty($column['score'])): ?>
                <span class="text-right" title="<?= t('Score') ?>">
                    <?= $column['score'] ?>
                </span>
            <?php endif ?>

            <?php if ($column['task_limit']): ?>
                <span title="<?= t('Task limit') ?>">
                    (<span id="task-number-column-<?= $column['id'] ?>"><?= $column['nb_tasks'] ?></span>/<?= $this->text->e($column['task_limit']) ?>)
                </span>
            <?php else: ?>
                <span title="<?= t('Task count') ?>" class="board-column-header-task-count">
                    (<span id="task-number-column-<?= $column['id'] ?>"><?= $column['nb_tasks'] ?></span>)
                </span>
            <?php endif ?>
        </div>

    </th>
    <?php endforeach ?>
</tr>
