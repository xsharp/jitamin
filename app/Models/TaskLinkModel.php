<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Model;

use Jitamin\Foundation\Database\Model;

/**
 * TaskLink model.
 */
class TaskLinkModel extends Model
{
    /**
     * SQL table name.
     *
     * @var string
     */
    const TABLE = 'task_has_links';

    /**
     * Events.
     *
     * @var string
     */
    const EVENT_CREATE_UPDATE = 'task_internal_link.create_update';
    const EVENT_DELETE = 'task_internal_link.delete';

    /**
     * Get projectId from $task_link_id.
     *
     * @param int $task_link_id
     *
     * @return int
     */
    public function getProjectId($task_link_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq(self::TABLE.'.id', $task_link_id)
            ->join(TaskModel::TABLE, 'id', 'task_id')
            ->findOneColumn(TaskModel::TABLE.'.project_id') ?: 0;
    }

    /**
     * Get a task link.
     *
     * @param int $task_link_id Task link id
     *
     * @return array
     */
    public function getById($task_link_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.id',
                self::TABLE.'.opposite_task_id',
                self::TABLE.'.task_id',
                self::TABLE.'.link_id',
                LinkModel::TABLE.'.label',
                LinkModel::TABLE.'.opposite_id AS opposite_link_id'
            )
            ->eq(self::TABLE.'.id', $task_link_id)
            ->join(LinkModel::TABLE, 'id', 'link_id')
            ->findOne();
    }

    /**
     * Get the opposite task link (use the unique index task_has_links_unique).
     *
     * @param array $task_link
     *
     * @return array
     */
    public function getOppositeTaskLink(array $task_link)
    {
        $opposite_link_id = $this->linkModel->getOppositeLinkId($task_link['link_id']);

        return $this->db->table(self::TABLE)
                    ->eq('opposite_task_id', $task_link['task_id'])
                    ->eq('task_id', $task_link['opposite_task_id'])
                    ->eq('link_id', $opposite_link_id)
                    ->findOne();
    }

    /**
     * Get all links attached to a task.
     *
     * @param int $task_id Task id
     *
     * @return array
     */
    public function getAll($task_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->columns(
                        self::TABLE.'.id',
                        self::TABLE.'.opposite_task_id AS task_id',
                        LinkModel::TABLE.'.label',
                        TaskModel::TABLE.'.title',
                        TaskModel::TABLE.'.is_active',
                        TaskModel::TABLE.'.project_id',
                        TaskModel::TABLE.'.column_id',
                        TaskModel::TABLE.'.color_id',
                        TaskModel::TABLE.'.time_spent AS task_time_spent',
                        TaskModel::TABLE.'.time_estimated AS task_time_estimated',
                        TaskModel::TABLE.'.owner_id AS task_assignee_id',
                        UserModel::TABLE.'.username AS task_assignee_username',
                        UserModel::TABLE.'.name AS task_assignee_name',
                        ColumnModel::TABLE.'.title AS column_title',
                        ProjectModel::TABLE.'.name AS project_name'
                    )
                    ->eq(self::TABLE.'.task_id', $task_id)
                    ->join(LinkModel::TABLE, 'id', 'link_id')
                    ->join(TaskModel::TABLE, 'id', 'opposite_task_id')
                    ->join(ColumnModel::TABLE, 'id', 'column_id', TaskModel::TABLE)
                    ->join(UserModel::TABLE, 'id', 'owner_id', TaskModel::TABLE)
                    ->join(ProjectModel::TABLE, 'id', 'project_id', TaskModel::TABLE)
                    ->asc(LinkModel::TABLE.'.id')
                    ->desc(ColumnModel::TABLE.'.position')
                    ->desc(TaskModel::TABLE.'.is_active')
                    ->asc(TaskModel::TABLE.'.position')
                    ->asc(TaskModel::TABLE.'.id')
                    ->findAll();
    }

    /**
     * Get all links attached to a task grouped by label.
     *
     * @param int $task_id Task id
     *
     * @return array
     */
    public function getAllGroupedByLabel($task_id)
    {
        $links = $this->getAll($task_id);
        $result = [];

        foreach ($links as $link) {
            if (!isset($result[$link['label']])) {
                $result[$link['label']] = [];
            }

            $result[$link['label']][] = $link;
        }

        return $result;
    }

    /**
     * Create a new link.
     *
     * @param int $task_id          Task id
     * @param int $opposite_task_id Opposite task id
     * @param int $link_id          Link id
     *
     * @return int|bool
     */
    public function create($task_id, $opposite_task_id, $link_id)
    {
        $this->db->startTransaction();

        $opposite_link_id = $this->linkModel->getOppositeLinkId($link_id);
        $task_link_id1 = $this->createTaskLink($task_id, $opposite_task_id, $link_id);
        $task_link_id2 = $this->createTaskLink($opposite_task_id, $task_id, $opposite_link_id);

        if ($task_link_id1 === false || $task_link_id2 === false) {
            $this->db->cancelTransaction();

            return false;
        }

        $this->db->closeTransaction();
        $this->fireEvents([$task_link_id1, $task_link_id2], self::EVENT_CREATE_UPDATE);

        return $task_link_id1;
    }

    /**
     * Update a task link.
     *
     * @param int $task_link_id     Task link id
     * @param int $task_id          Task id
     * @param int $opposite_task_id Opposite task id
     * @param int $link_id          Link id
     *
     * @return bool
     */
    public function update($task_link_id, $task_id, $opposite_task_id, $link_id)
    {
        $this->db->startTransaction();

        $task_link = $this->getById($task_link_id);
        $opposite_task_link = $this->getOppositeTaskLink($task_link);
        $opposite_link_id = $this->linkModel->getOppositeLinkId($link_id);

        $result1 = $this->updateTaskLink($task_link_id, $task_id, $opposite_task_id, $link_id);
        $result2 = $this->updateTaskLink($opposite_task_link['id'], $opposite_task_id, $task_id, $opposite_link_id);

        if ($result1 === false || $result2 === false) {
            $this->db->cancelTransaction();

            return false;
        }

        $this->db->closeTransaction();
        $this->fireEvents([$task_link_id, $opposite_task_link['id']], self::EVENT_CREATE_UPDATE);

        return true;
    }

    /**
     * Remove a link between two tasks.
     *
     * @param int $task_link_id
     *
     * @return bool
     */
    public function remove($task_link_id)
    {
        $this->taskLinkEventJob->execute($task_link_id, self::EVENT_DELETE);

        $this->db->startTransaction();

        $link = $this->getById($task_link_id);
        $link_id = $this->linkModel->getOppositeLinkId($link['link_id']);

        $result1 = $this->db
            ->table(self::TABLE)
            ->eq('id', $task_link_id)
            ->remove();

        $result2 = $this->db
            ->table(self::TABLE)
            ->eq('opposite_task_id', $link['task_id'])
            ->eq('task_id', $link['opposite_task_id'])
            ->eq('link_id', $link_id)
            ->remove();

        if ($result1 === false || $result2 === false) {
            $this->db->cancelTransaction();

            return false;
        }

        $this->db->closeTransaction();

        return true;
    }

    /**
     * Publish events.
     *
     * @param int[]  $task_link_ids
     * @param string $eventName
     */
    protected function fireEvents(array $task_link_ids, $eventName)
    {
        foreach ($task_link_ids as $task_link_id) {
            $this->queueManager->push($this->taskLinkEventJob->withParams($task_link_id, $eventName));
        }
    }

    /**
     * Create task link.
     *
     * @param int $task_id
     * @param int $opposite_task_id
     * @param int $link_id
     *
     * @return int|bool
     */
    protected function createTaskLink($task_id, $opposite_task_id, $link_id)
    {
        return $this->db->table(self::TABLE)->persist([
            'task_id'          => $task_id,
            'opposite_task_id' => $opposite_task_id,
            'link_id'          => $link_id,
        ]);
    }

    /**
     * Update task link.
     *
     * @param int $task_link_id
     * @param int $task_id
     * @param int $opposite_task_id
     * @param int $link_id
     *
     * @return bool
     */
    protected function updateTaskLink($task_link_id, $task_id, $opposite_task_id, $link_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $task_link_id)->update([
            'task_id'          => $task_id,
            'opposite_task_id' => $opposite_task_id,
            'link_id'          => $link_id,
        ]);
    }
}
