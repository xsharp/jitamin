<p class="activity-title">
    <?php $assignee = $task['assignee_name'] ?: $task['assignee_username'] ?>

    <?php if (!empty($assignee)): ?>
        <?= l('%s changed the assignee of the task %s to %s',
                $this->url->link($author, 'Profile/ProfileController', 'show', ['user_id' => $author_username]),
                $this->url->link(t('#%d', $task['id']), 'Task/TaskController', 'show', ['task_id' => $task['id'], 'project_id' => $task['project_id']]),
                $this->text->e($assignee)
            ) ?>
    <?php else: ?>
        <?= l('%s removed the assignee of the task %s', $this->text->e($author), $this->url->link(t('#%d', $task['id']), 'Task/TaskController', 'show', ['task_id' => $task['id'], 'project_id' => $task['project_id']])) ?>
    <?php endif ?>
    <small class="activity-date"><?= $this->dt->datetime($date_creation) ?></small>
</p>
<div class="activity-description">
    <p class="activity-task-title"><?= $this->text->e($task['title']) ?></p>
</div>
