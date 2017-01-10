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
use Jitamin\Foundation\Security\Role;

/**
 * Project Group Role.
 */
class ProjectGroupRoleModel extends Model
{
    /**
     * SQL table name.
     *
     * @var string
     */
    const TABLE = 'project_has_groups';

    /**
     * Get the list of project visible by the given user according to groups.
     *
     * @param int   $user_id
     * @param array $status
     *
     * @return array
     */
    public function getProjectsByUser($user_id, $status = [ProjectModel::ACTIVE, ProjectModel::INACTIVE])
    {
        return $this->db
            ->hashtable(ProjectModel::TABLE)
            ->join(self::TABLE, 'project_id', 'id')
            ->join(GroupMemberModel::TABLE, 'group_id', 'group_id', self::TABLE)
            ->eq(GroupMemberModel::TABLE.'.user_id', $user_id)
            ->in(ProjectModel::TABLE.'.is_active', $status)
            ->getAll(ProjectModel::TABLE.'.id', ProjectModel::TABLE.'.name');
    }

    /**
     * For a given project get the role of the specified user.
     *
     * @param int $project_id
     * @param int $user_id
     *
     * @return string
     */
    public function getUserRole($project_id, $user_id)
    {
        $roles = $this->db->table(self::TABLE)
            ->join(GroupMemberModel::TABLE, 'group_id', 'group_id', self::TABLE)
            ->eq(GroupMemberModel::TABLE.'.user_id', $user_id)
            ->eq(self::TABLE.'.project_id', $project_id)
            ->findAllByColumn('role');

        return $this->projectAccessMap->getHighestRole($roles);
    }

    /**
     * Get all groups associated directly to the project.
     *
     * @param int $project_id
     *
     * @return array
     */
    public function getGroups($project_id)
    {
        return $this->db->table(self::TABLE)
            ->columns(GroupModel::TABLE.'.id', GroupModel::TABLE.'.name', self::TABLE.'.role')
            ->join(GroupModel::TABLE, 'id', 'group_id')
            ->eq('project_id', $project_id)
            ->asc('name')
            ->findAll();
    }

    /**
     * From groups get all users associated to the project.
     *
     * @param int $project_id
     *
     * @return array
     */
    public function getUsers($project_id)
    {
        return $this->db->table(self::TABLE)
            ->columns(UserModel::TABLE.'.id', UserModel::TABLE.'.username', UserModel::TABLE.'.name', self::TABLE.'.role')
            ->join(GroupMemberModel::TABLE, 'group_id', 'group_id', self::TABLE)
            ->join(UserModel::TABLE, 'id', 'user_id', GroupMemberModel::TABLE)
            ->eq(self::TABLE.'.project_id', $project_id)
            ->asc(UserModel::TABLE.'.username')
            ->findAll();
    }

    /**
     * From groups get all users assignable to tasks.
     *
     * @param int $project_id
     *
     * @return array
     */
    public function getAssignableUsers($project_id)
    {
        return $this->db->table(UserModel::TABLE)
            ->columns(UserModel::TABLE.'.id', UserModel::TABLE.'.username', UserModel::TABLE.'.name')
            ->join(GroupMemberModel::TABLE, 'user_id', 'id', UserModel::TABLE)
            ->join(self::TABLE, 'group_id', 'group_id', GroupMemberModel::TABLE)
            ->eq(self::TABLE.'.project_id', $project_id)
            ->eq(UserModel::TABLE.'.is_active', 1)
            ->in(self::TABLE.'.role', [Role::PROJECT_MANAGER, Role::PROJECT_MEMBER])
            ->asc(UserModel::TABLE.'.username')
            ->findAll();
    }

    /**
     * Add a group to the project.
     *
     * @param int    $project_id
     * @param int    $group_id
     * @param string $role
     *
     * @return bool
     */
    public function addGroup($project_id, $group_id, $role)
    {
        return $this->db->table(self::TABLE)->insert([
            'group_id'   => $group_id,
            'project_id' => $project_id,
            'role'       => $role,
        ]);
    }

    /**
     * Remove a group from the project.
     *
     * @param int $project_id
     * @param int $group_id
     *
     * @return bool
     */
    public function removeGroup($project_id, $group_id)
    {
        return $this->db->table(self::TABLE)->eq('group_id', $group_id)->eq('project_id', $project_id)->remove();
    }

    /**
     * Change a group role for the project.
     *
     * @param int    $project_id
     * @param int    $group_id
     * @param string $role
     *
     * @return bool
     */
    public function changeGroupRole($project_id, $group_id, $role)
    {
        return $this->db->table(self::TABLE)
            ->eq('group_id', $group_id)
            ->eq('project_id', $project_id)
            ->update([
                'role' => $role,
            ]);
    }

    /**
     * Copy group access from a project to another one.
     *
     * @param int $project_src_id Project Template
     * @param int $project_dst_id Project that receives the copy
     *
     * @return bool
     */
    public function duplicate($project_src_id, $project_dst_id)
    {
        $rows = $this->db->table(self::TABLE)->eq('project_id', $project_src_id)->findAll();

        foreach ($rows as $row) {
            $result = $this->db->table(self::TABLE)->save([
                'project_id' => $project_dst_id,
                'group_id'   => $row['group_id'],
                'role'       => $row['role'],
            ]);

            if (!$result) {
                return false;
            }
        }

        return true;
    }
}
