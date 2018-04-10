<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Http\Controllers;

use Jitamin\Foundation\Base;
use Jitamin\Foundation\Exceptions\AccessForbiddenException;
use Jitamin\Foundation\Exceptions\PageNotFoundException;

/**
 * Base Controller.
 */
abstract class Controller extends Base
{
    /**
     * Check webhook token.
     */
    protected function checkWebhookToken()
    {
        if ($this->settingModel->get('webhook_token') !== $this->request->getStringParam('token')) {
            throw AccessForbiddenException::getInstance()->withoutLayout();
        }
    }

    /**
     * Common method to get a task for task views.
     *
     * @throws PageNotFoundException
     * @throws AccessForbiddenException
     *
     * @return array
     */
    protected function getTask()
    {
        $project_id = $this->request->getIntegerParam('project_id');
        $task = $this->taskFinderModel->getDetails($this->request->getIntegerParam('task_id'));

        if (empty($task)) {
            throw new PageNotFoundException();
        }

        if ($project_id !== 0 && $project_id != $task['project_id']) {
            throw new AccessForbiddenException();
        }

        return $task;
    }

    /**
     * Get Task or Project file.
     *
     * @throws PageNotFoundException
     * @throws AccessForbiddenException
     *
     * @return array
     */
    protected function getFile()
    {
        $task_id = $this->request->getIntegerParam('task_id');
        $file_id = $this->request->getIntegerParam('file_id');
        $model = 'projectFileModel';

        if ($task_id > 0) {
            $model = 'taskFileModel';
            $project_id = $this->taskFinderModel->getProjectId($task_id);

            if ($project_id !== $this->request->getIntegerParam('project_id')) {
                throw new AccessForbiddenException();
            }
        }

        $file = $this->$model->getById($file_id);

        if (empty($file)) {
            throw new PageNotFoundException();
        }

        $file['model'] = $model;

        return $file;
    }

    /**
     * Common method to get a project.
     *
     * @param int $project_id Default project id
     *
     * @throws PageNotFoundException
     *
     * @return array
     */
    protected function getProject($project_id = 0)
    {
        $project_id = $this->request->getIntegerParam('project_id', $project_id);
        $project = $this->projectModel->getByIdWithOwner($project_id);
        if (empty($project)) {
            throw new PageNotFoundException();
        }

        return $project;
    }

    /**
     * Common method to get the user.
     *
     * @throws PageNotFoundException
     * @throws AccessForbiddenException
     *
     * @return array
     */
    protected function getUser()
    {
        $user = $this->userModel->getById($this->request->getIntegerParam('user_id', $this->userSession->getId()));

        if (empty($user)) {
            throw new PageNotFoundException();
        }

        if (!$this->userSession->isAdmin() && $this->userSession->getId() != $user['id']) {
            throw new AccessForbiddenException();
        }

        return $user;
    }

    /**
     * Get the current subtask.
     *
     * @throws PageNotFoundException
     *
     * @return array
     */
    protected function getSubtask()
    {
        $subtask = $this->subtaskModel->getById($this->request->getIntegerParam('subtask_id'));

        if (empty($subtask)) {
            throw new PageNotFoundException();
        }

        return $subtask;
    }
}
