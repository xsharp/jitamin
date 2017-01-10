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
 * Class ColumnMoveRestrictionModel.
 */
class ColumnMoveRestrictionModel extends Model
{
    /**
     * SQL table name.
     *
     * @var string
     */
    const TABLE = 'column_has_move_restrictions';

    /**
     * Fetch one restriction.
     *
     * @param int $project_id
     * @param int $restriction_id
     *
     * @return array|null
     */
    public function getById($project_id, $restriction_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.restriction_id',
                self::TABLE.'.project_id',
                self::TABLE.'.role_id',
                self::TABLE.'.src_column_id',
                self::TABLE.'.dst_column_id',
                'pr.role',
                'sc.title as src_column_title',
                'dc.title as dst_column_title'
            )
            ->left(ColumnModel::TABLE, 'sc', 'id', self::TABLE, 'src_column_id')
            ->left(ColumnModel::TABLE, 'dc', 'id', self::TABLE, 'dst_column_id')
            ->left(ProjectRoleModel::TABLE, 'pr', 'role_id', self::TABLE, 'role_id')
            ->eq(self::TABLE.'.project_id', $project_id)
            ->eq(self::TABLE.'.restriction_id', $restriction_id)
            ->findOne();
    }

    /**
     * Get all project column restrictions.
     *
     * @param int $project_id
     *
     * @return array
     */
    public function getAll($project_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.restriction_id',
                self::TABLE.'.project_id',
                self::TABLE.'.role_id',
                self::TABLE.'.src_column_id',
                self::TABLE.'.dst_column_id',
                'pr.role',
                'sc.title as src_column_title',
                'dc.title as dst_column_title'
            )
            ->left(ColumnModel::TABLE, 'sc', 'id', self::TABLE, 'src_column_id')
            ->left(ColumnModel::TABLE, 'dc', 'id', self::TABLE, 'dst_column_id')
            ->left(ProjectRoleModel::TABLE, 'pr', 'role_id', self::TABLE, 'role_id')
            ->eq(self::TABLE.'.project_id', $project_id)
            ->findAll();
    }

    /**
     * Get all sortable column Ids.
     *
     * @param int    $project_id
     * @param string $role
     *
     * @return array
     */
    public function getSortableColumns($project_id, $role)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(self::TABLE.'.src_column_id', self::TABLE.'.dst_column_id')
            ->left(ProjectRoleModel::TABLE, 'pr', 'role_id', self::TABLE, 'role_id')
            ->eq(self::TABLE.'.project_id', $project_id)
            ->eq('pr.role', $role)
            ->findAll();
    }

    /**
     * Create a new column restriction.
     *
     * @param int $project_id
     * @param int $role_id
     * @param int $src_column_id
     * @param int $dst_column_id
     *
     * @return bool|int
     */
    public function create($project_id, $role_id, $src_column_id, $dst_column_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->persist([
                'project_id'    => $project_id,
                'role_id'       => $role_id,
                'src_column_id' => $src_column_id,
                'dst_column_id' => $dst_column_id,
            ]);
    }

    /**
     * Remove a permission.
     *
     * @param int $restriction_id
     *
     * @return bool
     */
    public function remove($restriction_id)
    {
        return $this->db->table(self::TABLE)->eq('restriction_id', $restriction_id)->remove();
    }
}
