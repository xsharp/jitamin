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
use Jitamin\Foundation\Translator;

/**
 * User Notification.
 */
class UserNotificationModel extends Model
{
    /**
     * Send notifications to people.
     *
     * @param string $event_name
     * @param array  $event_data
     */
    public function sendNotifications($event_name, array $event_data)
    {
        $users = $this->getUsersWithNotificationEnabled($event_data['task']['project_id'], $this->userSession->getId());

        foreach ($users as $user) {
            if ($this->userNotificationFilterModel->shouldReceiveNotification($user, $event_data)) {
                $this->sendUserNotification($user, $event_name, $event_data);
            }
        }
    }

    /**
     * Send notification to someone.
     *
     * @param array  $user       User
     * @param string $event_name
     * @param array  $event_data
     */
    public function sendUserNotification(array $user, $event_name, array $event_data)
    {
        Translator::unload();

        // Use the user language otherwise use the application language (do not use the session language)
        if (!empty($user['language'])) {
            Translator::load($user['language']);
        } else {
            Translator::load($this->settingModel->get('application_language', 'en_US'));
        }

        foreach ($this->userNotificationTypeModel->getSelectedTypes($user['id']) as $type) {
            $this->userNotificationTypeModel->getType($type)->notifyUser($user, $event_name, $event_data);
        }

        // Restore locales
        $this->languageModel->loadCurrentLanguage();
    }

    /**
     * Get a list of people with notifications enabled.
     *
     * @param int $project_id      Project id
     * @param int $exclude_user_id User id to exclude
     *
     * @return array
     */
    public function getUsersWithNotificationEnabled($project_id, $exclude_user_id = 0)
    {
        if ($this->projectPermissionModel->isEverybodyAllowed($project_id)) {
            return $this->getEverybodyWithNotificationEnabled($exclude_user_id);
        }

        $users = [];
        $members = $this->getProjectUserMembersWithNotificationEnabled($project_id, $exclude_user_id);
        $groups = $this->getProjectGroupMembersWithNotificationEnabled($project_id, $exclude_user_id);

        foreach (array_merge($members, $groups) as $user) {
            if (!isset($users[$user['id']])) {
                $users[$user['id']] = $user;
            }
        }

        return array_values($users);
    }

    /**
     * Enable notification for someone.
     *
     * @param int $user_id
     *
     * @return bool
     */
    public function enableNotification($user_id)
    {
        return $this->db->table(UserModel::TABLE)->eq('id', $user_id)->update(['notifications_enabled' => 1]);
    }

    /**
     * Disable notification for someone.
     *
     * @param int $user_id
     *
     * @return bool
     */
    public function disableNotification($user_id)
    {
        return $this->db->table(UserModel::TABLE)->eq('id', $user_id)->update(['notifications_enabled' => 0]);
    }

    /**
     * Save settings for the given user.
     *
     * @param int   $user_id User id
     * @param array $values  Form values
     */
    public function saveSettings($user_id, array $values)
    {
        $types = empty($values['notification_types']) ? [] : array_keys($values['notification_types']);

        if (!empty($types)) {
            $this->enableNotification($user_id);
        } else {
            $this->disableNotification($user_id);
        }

        $filter = empty($values['notifications_filter']) ? UserNotificationFilterModel::FILTER_BOTH : $values['notifications_filter'];
        $project_ids = empty($values['notification_projects']) ? [] : array_keys($values['notification_projects']);

        $this->userNotificationFilterModel->saveFilter($user_id, $filter);
        $this->userNotificationFilterModel->saveSelectedProjects($user_id, $project_ids);
        $this->userNotificationTypeModel->saveSelectedTypes($user_id, $types);
    }

    /**
     * Read user settings to display the form.
     *
     * @param int $user_id User id
     *
     * @return array
     */
    public function readSettings($user_id)
    {
        $values = $this->db->table(UserModel::TABLE)->eq('id', $user_id)->columns('notifications_enabled', 'notifications_filter')->findOne();
        $values['notification_types'] = $this->userNotificationTypeModel->getSelectedTypes($user_id);
        $values['notification_projects'] = $this->userNotificationFilterModel->getSelectedProjects($user_id);

        return $values;
    }

    /**
     * Get a list of project members with notification enabled.
     *
     * @param int $project_id      Project id
     * @param int $exclude_user_id User id to exclude
     *
     * @return array
     */
    private function getProjectUserMembersWithNotificationEnabled($project_id, $exclude_user_id)
    {
        return $this->db
            ->table(ProjectUserRoleModel::TABLE)
            ->columns(UserModel::TABLE.'.id', UserModel::TABLE.'.username', UserModel::TABLE.'.name', UserModel::TABLE.'.email', UserModel::TABLE.'.language', UserModel::TABLE.'.notifications_filter')
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->eq(ProjectUserRoleModel::TABLE.'.project_id', $project_id)
            ->eq(UserModel::TABLE.'.notifications_enabled', '1')
            ->eq(UserModel::TABLE.'.is_active', 1)
            ->neq(UserModel::TABLE.'.id', $exclude_user_id)
            ->findAll();
    }

    /**
     * Get a list of group members with notification enabled.
     *
     * @param int $project_id      Project id
     * @param int $exclude_user_id User id to exclude
     *
     * @return array
     */
    private function getProjectGroupMembersWithNotificationEnabled($project_id, $exclude_user_id)
    {
        return $this->db
            ->table(ProjectGroupRoleModel::TABLE)
            ->columns(UserModel::TABLE.'.id', UserModel::TABLE.'.username', UserModel::TABLE.'.name', UserModel::TABLE.'.email', UserModel::TABLE.'.language', UserModel::TABLE.'.notifications_filter')
            ->join(GroupMemberModel::TABLE, 'group_id', 'group_id', ProjectGroupRoleModel::TABLE)
            ->join(UserModel::TABLE, 'id', 'user_id', GroupMemberModel::TABLE)
            ->eq(ProjectGroupRoleModel::TABLE.'.project_id', $project_id)
            ->eq(UserModel::TABLE.'.notifications_enabled', '1')
            ->neq(UserModel::TABLE.'.id', $exclude_user_id)
            ->eq(UserModel::TABLE.'.is_active', 1)
            ->findAll();
    }

    /**
     * Get a list of project members with notification enabled.
     *
     * @param int $exclude_user_id User id to exclude
     *
     * @return array
     */
    private function getEverybodyWithNotificationEnabled($exclude_user_id)
    {
        return $this->db
            ->table(UserModel::TABLE)
            ->columns(UserModel::TABLE.'.id', UserModel::TABLE.'.username', UserModel::TABLE.'.name', UserModel::TABLE.'.email', UserModel::TABLE.'.language', UserModel::TABLE.'.notifications_filter')
            ->eq('notifications_enabled', '1')
            ->neq(UserModel::TABLE.'.id', $exclude_user_id)
            ->eq(UserModel::TABLE.'.is_active', 1)
            ->findAll();
    }
}
