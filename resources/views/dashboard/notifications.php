<?php if (empty($notifications)): ?>
<p class="alert"><?= t('You have no unread notifications') ?></p>
<?php else: ?>
    <div class="text-right">
        <i class="fa fa-check-square-o fa-fw"></i>
        <?= $this->url->link(t('Mark all as read'), 'Dashboard/NotificationController', 'flush', ['user_id' => $user['id']]) ?>
    </div>

    <table class="table-striped table-scrolling table-small">
        <tr>
            <th class="column-5">ID</th>
            <th class="column-10"><?= t('Project') ?></th>
            <th><?= t('Notification') ?></th>
            <th class="column-10"><?= t('Author') ?></th>
            <th class="column-15"><?= t('Date') ?></th>
            <th class="column-15"><?= t('Action') ?></th>
        </tr>
        <?php foreach ($notifications as $notification): ?>
        <tr>
            <td><?= $notification['id'] ?></td>
            <td>
                <?php if (isset($notification['event_data']['task']['project_name'])): ?>
                    <?= $this->url->link(
                            $this->text->e($notification['event_data']['task']['project_name']),
                            'Project/ProjectController',
                            'show',
                            ['project_id' => $notification['event_data']['task']['project_id']]
                        )
                    ?>
                <?php elseif (isset($notification['event_data']['project_name'])): ?>
                    <?= $this->text->e($notification['event_data']['project_name']) ?>
                <?php endif ?>
            </td>
            <td>
                <?php if ($this->text->contains($notification['event_name'], 'subtask')): ?>
                    <i class="fa fa-tasks fa-fw"></i>
                <?php elseif ($this->text->contains($notification['event_name'], 'task.move')): ?>
                    <i class="fa fa-arrows-alt fa-fw"></i>
                <?php elseif ($this->text->contains($notification['event_name'], 'task.overdue')): ?>
                    <i class="fa fa-calendar-times-o fa-fw"></i>
                <?php elseif ($this->text->contains($notification['event_name'], 'task')): ?>
                    <i class="fa fa-newspaper-o fa-fw"></i>
                <?php elseif ($this->text->contains($notification['event_name'], 'comment')): ?>
                    <i class="fa fa-comments-o fa-fw"></i>
                <?php elseif ($this->text->contains($notification['event_name'], 'file')): ?>
                    <i class="fa fa-file-o fa-fw"></i>
                <?php endif ?>

                <?php if ($this->text->contains($notification['event_name'], 'task.overdue') && count($notification['event_data']['tasks']) > 1): ?>
                    <?= $notification['title'] ?>
                <?php else: ?>
                    <?= $this->url->link($notification['title'], 'Dashboard/NotificationController', 'redirect', ['notification_id' => $notification['id'], 'user_id' => $user['id']]) ?>
                <?php endif ?>
            </td>
            <td>
            <?php if ($this->text->contains($notification['event_name'], 'comment')): ?>
                <?= $notification['event_data']['comment']['username'] ?>
            <?php else: ?>
                <?= $notification['event_data']['task']['assignee_username'] ?: $notification['event_data']['task']['creator_username'] ?>
            <?php endif ?>
            </td>
            <td>
                <?= $this->dt->datetime($notification['date_creation']) ?>
            </td>
            <td>
                <i class="fa fa-check fa-fw"></i>
                <?= $this->url->link(t('Mark as read'), 'Dashboard/NotificationController', 'remove', ['user_id' => $user['id'], 'notification_id' => $notification['id']]) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>
