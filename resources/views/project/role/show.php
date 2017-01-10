<div class="page-header">
    <h2><?= t('Custom Project Roles') ?></h2>
    <ul>
        <li>
            <i class="fa fa-plus fa-fw" aria-hidden="true"></i>
            <?= $this->url->link(t('Add a new custom role'), 'Project/ProjectRoleController', 'create', ['project_id' => $project['id']], false, 'popover') ?>
        </li>
    </ul>
</div>

<?php if (empty($roles)): ?>
    <div class="alert"><?= t('There is no custom role for this project.') ?></div>
<?php else: ?>
    <?php foreach ($roles as $role): ?>
    <table class="table-striped">
        <tr>
            <th>
                <div class="dropdown">
                    <a href="#" class="dropdown-menu"><?= t('Restrictions for the role "%s"', $role['role']) ?> <i class="fa fa-caret-down"></i></a>
                    <ul>
                        <li>
                            <i class="fa fa-plus fa-fw" aria-hidden="true"></i>
                            <?= $this->url->link(t('Add a new project restriction'), 'Project/ProjectRoleRestrictionController', 'create', ['project_id' => $project['id'], 'role_id' => $role['role_id']], false, 'popover') ?>
                        </li>
                        <li>
                            <i class="fa fa-plus fa-fw" aria-hidden="true"></i>
                            <?= $this->url->link(t('Add a new drag and drop restriction'), 'ColumnMoveRestrictionController', 'create', ['project_id' => $project['id'], 'role_id' => $role['role_id']], false, 'popover') ?>
                        </li>
                        <li>
                            <i class="fa fa-plus fa-fw" aria-hidden="true"></i>
                            <?= $this->url->link(t('Add a new column restriction'), 'ColumnRestrictionController', 'create', ['project_id' => $project['id'], 'role_id' => $role['role_id']], false, 'popover') ?>
                        </li>
                        <li>
                            <i class="fa fa-pencil fa-fw" aria-hidden="true"></i>
                            <?= $this->url->link(t('Edit this role'), 'Project/ProjectRoleController', 'edit', ['project_id' => $project['id'], 'role_id' => $role['role_id']], false, 'popover') ?>
                        </li>
                        <li>
                            <i class="fa fa-trash-o fa-fw" aria-hidden="true"></i>
                            <?= $this->url->link(t('Remove this role'), 'Project/ProjectRoleController', 'remove', ['project_id' => $project['id'], 'role_id' => $role['role_id']], false, 'popover') ?>
                        </li>
                    </ul>
                </div>
            </th>
            <th class="column-15">
                <?= t('Actions') ?>
            </th>
        </tr>
        <?php if (empty($role['project_restrictions']) && empty($role['column_restrictions']) && empty($role['column_move_restrictions'])): ?>
            <tr>
                <td colspan="2"><?= t('There is no restriction for this role.') ?></td>
            </tr>
        <?php else: ?>
            <?php foreach ($role['project_restrictions'] as $restriction): ?>
                <tr>
                    <td>
                        <i class="fa fa-ban fa-fw" aria-hidden="true"></i>
                        <strong><?= t('Project') ?></strong>
                        <i class="fa fa-arrow-right fa-fw" aria-hidden="true"></i>
                        <?= $this->text->e($restriction['title']) ?>
                    </td>
                    <td>
                        <i class="fa fa-trash-o fa-fw" aria-hidden="true"></i>
                        <?= $this->url->link(t('Remove'), 'Project/ProjectRoleRestrictionController', 'remove', ['project_id' => $project['id'], 'restriction_id' => $restriction['restriction_id']], false, 'popover') ?>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php foreach ($role['column_restrictions'] as $restriction): ?>
                <tr>
                    <td>
                        <?php if (strpos($restriction['rule'], 'block') === 0): ?>
                            <i class="fa fa-ban fa-fw" aria-hidden="true"></i>
                        <?php else: ?>
                            <i class="fa fa-check-circle-o fa-fw" aria-hidden="true"></i>
                        <?php endif ?>
                        <strong><?= $this->text->e($restriction['column_title']) ?></strong>
                        <i class="fa fa-arrow-right fa-fw" aria-hidden="true"></i>
                        <?= $this->text->e($restriction['title']) ?>
                    </td>
                    <td>
                        <i class="fa fa-trash-o fa-fw" aria-hidden="true"></i>
                        <?= $this->url->link(t('Remove'), 'ColumnRestrictionController', 'remove', ['project_id' => $project['id'], 'restriction_id' => $restriction['restriction_id']], false, 'popover') ?>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php foreach ($role['column_move_restrictions'] as $restriction): ?>
                <tr>
                    <td>
                        <i class="fa fa-check-circle-o fa-fw" aria-hidden="true"></i>
                        <strong><?= $this->text->e($restriction['src_column_title']) ?> / <?= $this->text->e($restriction['dst_column_title']) ?></strong>
                        <i class="fa fa-arrow-right fa-fw" aria-hidden="true"></i>
                        <?= t('Only moving task between those columns is permitted') ?>
                    </td>
                    <td>
                        <i class="fa fa-trash-o fa-fw" aria-hidden="true"></i>
                        <?= $this->url->link(t('Remove'), 'ColumnMoveRestrictionController', 'remove', ['project_id' => $project['id'], 'restriction_id' => $restriction['restriction_id']], false, 'popover') ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif ?>
    </table>
    <?php endforeach ?>
<?php endif ?>
