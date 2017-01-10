<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Controller\Profile;

use Jitamin\Controller\Controller;
use Jitamin\Foundation\Controller\PageNotFoundException;
use Jitamin\Foundation\Security\Token;
use Jitamin\Model\ProjectModel;

/**
 * Class ProfileController.
 */
class ProfileController extends Controller
{
    /**
     * Public user profile.
     *
     * @throws PageNotFoundException
     */
    public function profile()
    {
        $column = $this->request->getStringParam('user_id');
        $method = is_numeric($column) ? 'getById' : 'getByUsername';

        $user = $this->userModel->{$method}($column);

        if (empty($user)) {
            throw new PageNotFoundException();
        }

        $this->response->html($this->helper->layout->app('profile/profile', [
            'title'  => $user['name'] ?: $user['username'],
            'events' => $this->helper->projectActivity->searchEvents('creator:'.$user['username'], 20),
            'user'   => $user,
        ], ''));
    }

    /**
     * Display user information.
     */
    public function show()
    {
        $user = $this->getUser();
        $this->response->html($this->helper->layout->profile('profile/show', [
            'user'      => $user,
            'timezones' => $this->timezoneModel->getTimezones(true),
            'languages' => $this->languageModel->getLanguages(true),
        ], ''));
    }

    /**
     * Display timesheet.
     */
    public function timesheet()
    {
        $user = $this->getUser();

        $subtask_paginator = $this->paginator
            ->setUrl('Profile/ProfileController', 'timesheet', ['user_id' => $user['id'], 'pagination' => 'subtasks'])
            ->setMax(20)
            ->setOrder('start')
            ->setDirection('DESC')
            ->setQuery($this->subtaskTimeTrackingModel->getUserQuery($user['id']))
            ->calculateOnlyIf($this->request->getStringParam('pagination') === 'subtasks');

        $this->response->html($this->helper->layout->profile('profile/timesheet', [
            'subtask_paginator' => $subtask_paginator,
            'user'              => $user,
        ], ''));
    }

    /**
     * Display last password reset.
     */
    public function passwordReset()
    {
        $user = $this->getUser();
        $this->response->html($this->helper->layout->profile('profile/password_reset', [
            'tokens' => $this->passwordResetModel->getAll($user['id']),
            'user'   => $user,
        ], ''));
    }

    /**
     * Display last connections.
     */
    public function lastLogin()
    {
        $user = $this->getUser();
        $this->response->html($this->helper->layout->profile('profile/last', [
            'last_logins' => $this->lastLoginModel->getAll($user['id']),
            'user'        => $user,
        ], ''));
    }

    /**
     * Display user sessions.
     */
    public function sessions()
    {
        $user = $this->getUser();
        $this->response->html($this->helper->layout->profile('profile/sessions', [
            'sessions' => $this->rememberMeSessionModel->getAll($user['id']),
            'user'     => $user,
        ], ''));
    }

    /**
     * Remove a "RememberMe" token.
     */
    public function removeSession()
    {
        $user = $this->getUser();
        $this->rememberMeSessionModel->remove($this->request->getIntegerParam('id'));
        $this->response->redirect($this->helper->url->to('Profile/ProfileController', 'sessions', ['user_id' => $user['id']]));
    }

    /**
     * Display user notifications.
     */
    public function notifications()
    {
        $user = $this->getUser();

        if ($this->request->isPost()) {
            $values = $this->request->getValues();
            $this->userNotificationModel->saveSettings($user['id'], $values);
            $this->flash->success(t('User updated successfully.'));

            return $this->response->redirect($this->helper->url->to('Profile/ProfileController', 'notifications', ['user_id' => $user['id']]));
        }

        return $this->response->html($this->helper->layout->profile('profile/notifications', [
            'projects'      => $this->projectUserRoleModel->getProjectsByUser($user['id'], [ProjectModel::ACTIVE]),
            'notifications' => $this->userNotificationModel->readSettings($user['id']),
            'types'         => $this->userNotificationTypeModel->getTypes(),
            'filters'       => $this->userNotificationFilterModel->getFilters(),
            'user'          => $user,
        ]));
    }

    /**
     * Display user integrations.
     */
    public function integrations()
    {
        $user = $this->getUser();

        if ($this->request->isPost()) {
            $values = $this->request->getValues();
            $this->userMetadataModel->save($user['id'], $values);
            $this->flash->success(t('User updated successfully.'));
            $this->response->redirect($this->helper->url->to('Profile/ProfileController', 'integrations', ['user_id' => $user['id']]));
        }

        $this->response->html($this->helper->layout->profile('profile/integrations', [
            'user'   => $user,
            'values' => $this->userMetadataModel->getAll($user['id']),
        ]));
    }

    /**
     * Display user api.
     */
    public function api()
    {
        $user = $this->getUser();

        return $this->response->html($this->helper->layout->profile('profile/api', [
            'user'  => $user,
            'title' => t('API User Access'),
        ]));
    }

    /**
     * Generate the api token.
     */
    public function generateApiToken()
    {
        $user = $this->getUser();
        $this->userModel->update([
            'id'        => $user['id'],
            'api_token' => Token::getToken(),
        ]);
        $this->response->redirect($this->helper->url->to('Profile/ProfileController', 'api', ['user_id' => $user['id']]));
    }

    /**
     * Remove the api token.
     */
    public function removeApiToken()
    {
        $user = $this->getUser();
        $this->userModel->update([
            'id'        => $user['id'],
            'api_token' => null,
        ]);
        $this->response->redirect($this->helper->url->to('Profile/ProfileController', 'api', ['user_id' => $user['id']]));
    }

    /**
     * Display external accounts.
     */
    public function external()
    {
        $user = $this->getUser();
        $this->response->html($this->helper->layout->profile('profile/external', [
            'last_logins' => $this->lastLoginModel->getAll($user['id']),
            'user'        => $user,
        ]));
    }

    /**
     * Public access management.
     */
    public function share()
    {
        $user = $this->getUser();
        $switch = $this->request->getStringParam('switch');

        if ($switch === 'enable' || $switch === 'disable') {
            if ($this->userModel->{$switch.'PublicAccess'}($user['id'])) {
                $this->flash->success(t('User updated successfully.'));
            } else {
                $this->flash->failure(t('Unable to update this user.'));
            }

            return $this->response->redirect($this->helper->url->to('Profile/ProfileController', 'share', ['user_id' => $user['id']]));
        }

        return $this->response->html($this->helper->layout->profile('profile/share', [
            'user'  => $user,
            'title' => t('Public access'),
        ]));
    }

    /**
     * Display a form to edit user information.
     *
     * @param array $values
     * @param array $errors
     *
     * @throws \Jitamin\Foundation\Controller\AccessForbiddenException
     * @throws \Jitamin\Foundation\Controller\PageNotFoundException
     */
    public function edit(array $values = [], array $errors = [])
    {
        $user = $this->getUser();

        if (empty($values)) {
            $values = $user;
            unset($values['password']);
        }

        return $this->response->html($this->helper->layout->profile('profile/edit', [
            'values'     => $values,
            'errors'     => $errors,
            'user'       => $user,
            'roles'      => $this->role->getApplicationRoles(),
        ]));
    }

    /**
     * Display a form to edit user preferences.
     *
     * @param array $values
     * @param array $errors
     *
     * @throws \Jitamin\Foundation\Controller\AccessForbiddenException
     * @throws \Jitamin\Foundation\Controller\PageNotFoundException
     */
    public function preferences(array $values = [], array $errors = [])
    {
        $user = $this->getUser();

        if (empty($values)) {
            $values = $user;
            unset($values['password']);
        }

        return $this->response->html($this->helper->layout->profile('profile/preferences', [
            'values'     => $values,
            'errors'     => $errors,
            'user'       => $user,
            'skins'      => $this->skinModel->getSkins(true),
            'layouts'    => $this->skinModel->getLayouts(true),
            'dashboards' => $this->skinModel->getDashboards(true),
            'timezones'  => $this->timezoneModel->getTimezones(true),
            'languages'  => $this->languageModel->getLanguages(true),
        ]));
    }

    /**
     * Update user information.
     */
    public function update()
    {
        $user = $this->getUser();
        $values = $this->request->getValues();
        $redirect = $this->request->getStringParam('redirect', 'edit');

        if (!$this->userSession->isAdmin()) {
            if (isset($values['role'])) {
                unset($values['role']);
            }
        }

        list($valid, $errors) = $this->userValidator->validateModification($values);

        if ($valid) {
            if ($this->userModel->update($values)) {
                $this->flash->success(t('User updated successfully.'));
            } else {
                $this->flash->failure(t('Unable to update your user.'));
            }

            return $this->response->redirect($this->helper->url->to('Profile/ProfileController', $redirect, ['user_id' => $user['id']]));
        }

        return $this->show($values, $errors);
    }

    /**
     * Password modification form.
     *
     * @param array $values
     * @param array $errors
     *
     * @throws \Jitamin\Foundation\Controller\AccessForbiddenException
     * @throws \Jitamin\Foundation\Controller\PageNotFoundException
     */
    public function changePassword(array $values = [], array $errors = [])
    {
        $user = $this->getUser();

        return $this->response->html($this->helper->layout->profile('profile/change_password', [
            'values' => $values + ['id' => $user['id']],
            'errors' => $errors,
            'user'   => $user,
        ]));
    }

    /**
     * Save new password.
     *
     * @throws \Jitamin\Foundation\Controller\AccessForbiddenException
     * @throws \Jitamin\Foundation\Controller\PageNotFoundException
     */
    public function savePassword()
    {
        $user = $this->getUser();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->userValidator->validatePasswordModification($values);

        if ($valid) {
            if ($this->userModel->update($values)) {
                $this->flash->success(t('Password modified successfully.'));
                $this->userLockingModel->resetFailedLogin($user['username']);
            } else {
                $this->flash->failure(t('Unable to change the password.'));
            }

            return $this->response->redirect($this->helper->url->to('Profile/ProfileController', 'show', ['user_id' => $user['id']]));
        }

        return $this->changePassword($values, $errors);
    }
}
