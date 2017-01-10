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
 * Comment model.
 */
class CommentModel extends Model
{
    /**
     * SQL table name.
     *
     * @var string
     */
    const TABLE = 'comments';

    /**
     * Events.
     *
     * @var string
     */
    const EVENT_UPDATE = 'comment.update';
    const EVENT_CREATE = 'comment.create';
    const EVENT_DELETE = 'comment.delete';
    const EVENT_USER_MENTION = 'comment.user.mention';

    /**
     * Get projectId from commentId.
     *
     * @param int $comment_id
     *
     * @return int
     */
    public function getProjectId($comment_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq(self::TABLE.'.id', $comment_id)
            ->join(TaskModel::TABLE, 'id', 'task_id')
            ->findOneColumn(TaskModel::TABLE.'.project_id') ?: 0;
    }

    /**
     * Get all comments for a given task.
     *
     * @param int    $task_id Task id
     * @param string $sorting ASC/DESC
     *
     * @return array
     */
    public function getAll($task_id, $sorting = 'ASC')
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.id',
                self::TABLE.'.date_creation',
                self::TABLE.'.task_id',
                self::TABLE.'.user_id',
                self::TABLE.'.comment',
                UserModel::TABLE.'.username',
                UserModel::TABLE.'.name',
                UserModel::TABLE.'.email',
                UserModel::TABLE.'.avatar_path'
            )
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->orderBy(self::TABLE.'.date_creation', $sorting)
            ->eq(self::TABLE.'.task_id', $task_id)
            ->findAll();
    }

    /**
     * Get a comment.
     *
     * @param int $comment_id Comment id
     *
     * @return array
     */
    public function getById($comment_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.id',
                self::TABLE.'.task_id',
                self::TABLE.'.user_id',
                self::TABLE.'.date_creation',
                self::TABLE.'.comment',
                self::TABLE.'.reference',
                UserModel::TABLE.'.username',
                UserModel::TABLE.'.name',
                UserModel::TABLE.'.email',
                UserModel::TABLE.'.avatar_path'
            )
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->eq(self::TABLE.'.id', $comment_id)
            ->findOne();
    }

    /**
     * Get the number of comments for a given task.
     *
     * @param int $task_id Task id
     *
     * @return int
     */
    public function count($task_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq(self::TABLE.'.task_id', $task_id)
            ->count();
    }

    /**
     * Create a new comment.
     *
     * @param array $values Form values
     *
     * @return bool|int
     */
    public function create(array $values)
    {
        $values['date_creation'] = time();
        $comment_id = $this->db->table(self::TABLE)->persist($values);

        if ($comment_id !== false) {
            $this->queueManager->push($this->commentEventJob->withParams($comment_id, self::EVENT_CREATE));
        }

        return $comment_id;
    }

    /**
     * Update a comment in the database.
     *
     * @param array $values Form values
     *
     * @return bool
     */
    public function update(array $values)
    {
        $result = $this->db
                    ->table(self::TABLE)
                    ->eq('id', $values['id'])
                    ->update(['comment' => $values['comment']]);

        if ($result) {
            $this->queueManager->push($this->commentEventJob->withParams($values['id'], self::EVENT_UPDATE));
        }

        return $result;
    }

    /**
     * Remove a comment.
     *
     * @param int $comment_id Comment id
     *
     * @return bool
     */
    public function remove($comment_id)
    {
        $this->commentEventJob->execute($comment_id, self::EVENT_DELETE);

        return $this->db->table(self::TABLE)->eq('id', $comment_id)->remove();
    }
}
