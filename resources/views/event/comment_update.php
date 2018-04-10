<p class="activity-title">
    <?= l('%s updated a comment on the task %s',
            $this->url->link($author, 'Profile/ProfileController', 'show', ['user_id' => $author_username]),
            $this->url->link(t('#%d', $task['id']), 'Task/TaskController', 'show', ['task_id' => $task['id'], 'project_id' => $task['project_id']])
        ) ?>
    <small class="activity-date"><?= $this->dt->datetime($date_creation) ?></small>
</p>
<div class="activity-description">
    <p class="activity-task-title"><?= $this->text->e($task['title']) ?></p>
    <?php if (!empty($comment['comment'])): ?>
        <div class="markdown"><?= $this->text->markdown($comment['comment']) ?></div>
    <?php endif ?>
</div>
