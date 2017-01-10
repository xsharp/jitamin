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
 * Custom Filter model.
 */
class CustomFilterModel extends Model
{
    /**
     * SQL table name.
     *
     * @var string
     */
    const TABLE = 'custom_filters';

    /**
     * Return the list of all allowed custom filters for a user and project.
     *
     * @param int $project_id Project id
     * @param int $user_id    User id
     *
     * @return array
     */
    public function getAll($project_id, $user_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                UserModel::TABLE.'.name as owner_name',
                UserModel::TABLE.'.username as owner_username',
                self::TABLE.'.id',
                self::TABLE.'.user_id',
                self::TABLE.'.project_id',
                self::TABLE.'.filter',
                self::TABLE.'.name',
                self::TABLE.'.is_shared',
                self::TABLE.'.append'
            )
            ->asc(self::TABLE.'.name')
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->beginOr()
            ->eq('is_shared', 1)
            ->eq('user_id', $user_id)
            ->closeOr()
            ->eq('project_id', $project_id)
            ->findAll();
    }

    /**
     * Get custom filter by id.
     *
     * @param int $filter_id
     *
     * @return array
     */
    public function getById($filter_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $filter_id)->findOne();
    }

    /**
     * Create a custom filter.
     *
     * @param array $values Form values
     *
     * @return bool|int
     */
    public function create(array $values)
    {
        return $this->db->table(self::TABLE)->persist($values);
    }

    /**
     * Update a custom filter.
     *
     * @param array $values Form values
     *
     * @return bool
     */
    public function update(array $values)
    {
        return $this->db->table(self::TABLE)
            ->eq('id', $values['id'])
            ->update($values);
    }

    /**
     * Remove a custom filter.
     *
     * @param int $filter_id
     *
     * @return bool
     */
    public function remove($filter_id)
    {
        return $this->db->table(self::TABLE)->eq('id', $filter_id)->remove();
    }
}
