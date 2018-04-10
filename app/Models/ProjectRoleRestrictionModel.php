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
 * Class ProjectRoleRestrictionModel.
 */
class ProjectRoleRestrictionModel extends Model
{
    /**
     * SQL table name.
     *
     * @var string
     */
    const TABLE = 'project_role_has_restrictions';

    const RULE_TASK_CREATION = 'task_creation';
    const RULE_TASK_OPEN_CLOSE = 'task_open_close';
    const RULE_TASK_MOVE = 'task_move';

    /**
     * Get rules.
     *
     * @return array
     */
    public function getRules()
    {
        return [
            self::RULE_TASK_CREATION   => t('Task creation is not permitted'),
            self::RULE_TASK_OPEN_CLOSE => t('Closing or opening a task is not permitted'),
            self::RULE_TASK_MOVE       => t('Moving a task is not permitted'),
        ];
    }

    /**
     * Get a single restriction.
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
            ->eq('project_id', $project_id)
            ->eq('restriction_id', $restriction_id)
            ->findOne();
    }

    /**
     * Get restrictions.
     *
     * @param int $project_id
     *
     * @return array
     */
    public function getAll($project_id)
    {
        $rules = $this->getRules();
        $restrictions = $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.restriction_id',
                self::TABLE.'.project_id',
                self::TABLE.'.role_id',
                self::TABLE.'.rule'
            )
            ->eq(self::TABLE.'.project_id', $project_id)
            ->findAll();

        foreach ($restrictions as &$restriction) {
            $restriction['title'] = $rules[$restriction['rule']];
        }

        return $restrictions;
    }

    /**
     * Get restrictions.
     *
     * @param int    $project_id
     * @param string $role
     *
     * @return array
     */
    public function getAllByRole($project_id, $role)
    {
        return $this->db
            ->table(self::TABLE)
            ->columns(
                self::TABLE.'.restriction_id',
                self::TABLE.'.project_id',
                self::TABLE.'.role_id',
                self::TABLE.'.rule',
                'pr.role'
            )
            ->eq(self::TABLE.'.project_id', $project_id)
            ->eq('role', $role)
            ->left(ProjectRoleModel::TABLE, 'pr', 'role_id', self::TABLE, 'role_id')
            ->findAll();
    }

    /**
     * Create a new restriction.
     *
     * @param int    $project_id
     * @param int    $role_id
     * @param string $rule
     *
     * @return bool|int
     */
    public function create($project_id, $role_id, $rule)
    {
        return $this->db->table(self::TABLE)
            ->persist([
                'project_id' => $project_id,
                'role_id'    => $role_id,
                'rule'       => $rule,
            ]);
    }

    /**
     * Remove a restriction.
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
