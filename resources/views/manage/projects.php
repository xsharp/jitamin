<div class="page-header">
    <?= $this->render('manage/_partials/nav') ?>
</div>
<?php if ($paginator->isEmpty()): ?>
    <p class="alert"><?= t('No project') ?></p>
<?php else: ?>
    <table class="table-striped table-scrolling">
        <tr>
            <th class="column-8"><?= $paginator->order(t('Id'), 'id') ?></th>
            <th class="column-8"><?= $paginator->order(t('Status'), 'is_active') ?></th>
            <th class="column-20"><?= $paginator->order(t('Project'), 'name') ?></th>
            <th class="column-10"><?= $paginator->order(t('Start date'), 'start_date') ?></th>
            <th class="column-10"><?= $paginator->order(t('End date'), 'end_date') ?></th>
            <th class="column-10"><?= $paginator->order(t('Owner'), 'owner_id') ?></th>
            <?php if ($this->user->hasAccess('Manage/ProjectUserOverviewController', 'managers')): ?>
                <th class="column-8"><?= t('Users') ?></th>
            <?php endif ?>
            <th><?= t('Columns') ?></th>
        </tr>
        <?php foreach ($paginator->getCollection() as $project): ?>
        <tr>
            <td>
                <?= $this->render('project/dropdown', ['project' => $project]) ?>
            </td>
            <td>
                <?php if ($project['is_active']): ?>
                    <?= t('Active') ?>
                <?php else: ?>
                    <?= t('Inactive') ?>
                <?php endif ?>
            </td>
            <td>
                <?= $this->url->link($this->text->e($project['name']), 'Project/ProjectController', 'show', ['project_id' => $project['id']]) ?>
                <?php if ($project['is_public']): ?>
                    <i class="fa fa-share-alt" title="<?= t('Shared project') ?>"></i>
                <?php endif ?>
                <?php if ($project['is_private']): ?>
                    <i class="fa fa-lock" title="<?= t('Private project') ?>"></i>
                <?php endif ?>
                <?php if (!empty($project['description'])): ?>
                    <span class="tooltip" title="<?= $this->text->markdownAttribute($project['description']) ?>">
                        <i class="fa fa-info-circle"></i>
                    </span>
                <?php endif ?>
            </td>
            <td>
                <?= $this->dt->date($project['start_date']) ?>
            </td>
            <td>
                <?= $this->dt->date($project['end_date']) ?>
            </td>
            <td>
                <?php if ($project['owner_id'] > 0): ?>
                    <?= $this->text->e($project['owner_name'] ?: $project['owner_username']) ?>
                <?php endif ?>
            </td>
            <?php if ($this->user->hasAccess('Manage/ProjectUserOverviewController', 'managers')): ?>
                <td>
                    <i class="fa fa-users"></i>
                    <a class="tooltip" title="<?= t('Members') ?>" data-href="<?= $this->url->href('Manage/ProjectUserOverviewController', 'users', ['project_id' => $project['id']]) ?>"><?= t('Members') ?></a>
                </td>
            <?php endif ?>
            <td class="dashboard-project-stats">
                <?php foreach ($project['columns'] as $column): ?>
                    <strong title="<?= t('Task count') ?>"><?= $column['nb_tasks'] ?></strong>
                    <small><?= $this->text->e($column['title']) ?></small>
                <?php endforeach ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>

    <?= $paginator ?>
<?php endif ?>
