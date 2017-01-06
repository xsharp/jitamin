<?php if (empty($users)): ?>
    <p><?= t('There is no project member.') ?></p>
<?php else: ?>
    <table class="table-small">
    <?php foreach ($roles as $role => $role_name): ?>
        <?php if (isset($users[$role])): ?>
        <tr><th><?= $role_name ?></th></tr>
            <?php foreach ($users[$role] as $user_id => $user): ?>
                <tr><td>
                <?= $this->url->link($this->text->e($user), 'Manage/ProjectUserOverviewController', 'opens', ['user_id' => $user_id]) ?>
                </td></tr>
            <?php endforeach ?>
        <?php endif ?>
    <?php endforeach ?>
    </table>
<?php endif ?>
