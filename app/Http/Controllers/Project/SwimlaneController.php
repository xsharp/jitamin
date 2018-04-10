<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Http\Controllers\Project;

use Jitamin\Foundation\Exceptions\AccessForbiddenException;
use Jitamin\Foundation\Exceptions\PageNotFoundException;
use Jitamin\Http\Controllers\Controller;
use Jitamin\Model\SwimlaneModel;

/**
 * Swimlanes Controller.
 */
class SwimlaneController extends Controller
{
    /**
     * List of swimlanes for a given project.
     */
    public function index()
    {
        $project = $this->getProject();

        $this->response->html($this->helper->layout->project('project/swimlane/index', [
            'default_swimlane'   => $this->swimlaneModel->getDefault($project['id']),
            'active_swimlanes'   => $this->swimlaneModel->getAllByStatus($project['id'], SwimlaneModel::ACTIVE),
            'inactive_swimlanes' => $this->swimlaneModel->getAllByStatus($project['id'], SwimlaneModel::INACTIVE),
            'project'            => $project,
            'title'              => t('Swimlanes'),
        ]));
    }

    /**
     * Create a new swimlane.
     *
     * @param array $values
     * @param array $errors
     *
     * @throws \Jitamin\Foundation\Exceptions\PageNotFoundException
     */
    public function create(array $values = [], array $errors = [])
    {
        $project = $this->getProject();

        $this->response->html($this->template->render('project/swimlane/create', [
            'values'  => $values + ['project_id' => $project['id']],
            'errors'  => $errors,
            'project' => $project,
        ]));
    }

    /**
     * Validate and save a new swimlane.
     */
    public function store()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();
        list($valid, $errors) = $this->swimlaneValidator->validateCreation($values);

        if ($valid) {
            if ($this->swimlaneModel->create($values) !== false) {
                $this->flash->success(t('Your swimlane have been created successfully.'));

                return $this->response->redirect($this->helper->url->to('Project/SwimlaneController', 'index', ['project_id' => $project['id']]));
            } else {
                $errors = ['name' => [t('Another swimlane with the same name exists in the project')]];
            }
        }

        return $this->create($values, $errors);
    }

    /**
     * Edit default swimlane (display the form).
     *
     * @param array $values
     * @param array $errors
     *
     * @throws \Jitamin\Foundation\Exceptions\PageNotFoundException
     */
    public function editDefault(array $values = [], array $errors = [])
    {
        $project = $this->getProject();
        $swimlane = $this->swimlaneModel->getDefault($project['id']);

        $this->response->html($this->helper->layout->project('project/swimlane/edit_default', [
            'values'  => empty($values) ? $swimlane : $values,
            'errors'  => $errors,
            'project' => $project,
        ]));
    }

    /**
     * Change the default swimlane.
     */
    public function updateDefault()
    {
        $project = $this->getProject();

        $values = $this->request->getValues() + ['show_default_swimlane' => 0];
        list($valid, $errors) = $this->swimlaneValidator->validateDefaultModification($values);

        if ($valid) {
            if ($this->swimlaneModel->updateDefault($values)) {
                $this->flash->success(t('The default swimlane have been updated successfully.'));

                return $this->response->redirect($this->helper->url->to('Project/SwimlaneController', 'index', ['project_id' => $project['id']]), true);
            } else {
                $this->flash->failure(t('Unable to update this swimlane.'));
            }
        }

        return $this->editDefault($values, $errors);
    }

    /**
     * Edit a swimlane (display the form).
     *
     * @param array $values
     * @param array $errors
     *
     * @throws \Jitamin\Foundation\Exceptions\PageNotFoundException
     */
    public function edit(array $values = [], array $errors = [])
    {
        $project = $this->getProject();
        $swimlane = $this->getSwimlane();

        $this->response->html($this->helper->layout->project('project/swimlane/edit', [
            'values'  => empty($values) ? $swimlane : $values,
            'errors'  => $errors,
            'project' => $project,
        ]));
    }

    /**
     * Edit a swimlane (validate the form and update the database).
     */
    public function update()
    {
        $project = $this->getProject();

        $values = $this->request->getValues();
        list($valid, $errors) = $this->swimlaneValidator->validateModification($values);

        if ($valid) {
            if ($this->swimlaneModel->update($values)) {
                $this->flash->success(t('Swimlane updated successfully.'));

                return $this->response->redirect($this->helper->url->to('Project/SwimlaneController', 'index', ['project_id' => $project['id']]));
            } else {
                $errors = ['name' => [t('Another swimlane with the same name exists in the project')]];
            }
        }

        return $this->edit($values, $errors);
    }

    /**
     * Remove a swimlane.
     */
    public function remove()
    {
        $project = $this->getProject();
        $swimlane = $this->getSwimlane();

        if ($this->request->isPost()) {
            $this->request->checkCSRFToken();
            if ($this->swimlaneModel->remove($project['id'], $swimlane['id'])) {
                $this->flash->success(t('Swimlane removed successfully.'));
            } else {
                $this->flash->failure(t('Unable to remove this swimlane.'));
            }

            return $this->response->redirect($this->helper->url->to('Project/SwimlaneController', 'index', ['project_id' => $project['id']]));
        }

        return $this->response->html($this->helper->layout->project('project/swimlane/remove', [
            'project'  => $project,
            'swimlane' => $swimlane,
        ]));
    }

    /**
     * Disable a swimlane.
     */
    public function disable()
    {
        $project = $this->getProject();
        $swimlane_id = $this->request->getIntegerParam('swimlane_id');

        if ($this->swimlaneModel->disable($project['id'], $swimlane_id)) {
            $this->flash->success(t('Swimlane updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update this swimlane.'));
        }

        $this->response->redirect($this->helper->url->to('Project/SwimlaneController', 'index', ['project_id' => $project['id']]));
    }

    /**
     * Disable default swimlane.
     */
    public function disableDefault()
    {
        $project = $this->getProject();

        if ($this->swimlaneModel->disableDefault($project['id'])) {
            $this->flash->success(t('Swimlane updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update this swimlane.'));
        }

        $this->response->redirect($this->helper->url->to('Project/SwimlaneController', 'index', ['project_id' => $project['id']]));
    }

    /**
     * Enable a swimlane.
     */
    public function enable()
    {
        $project = $this->getProject();
        $swimlane_id = $this->request->getIntegerParam('swimlane_id');

        if ($this->swimlaneModel->enable($project['id'], $swimlane_id)) {
            $this->flash->success(t('Swimlane updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update this swimlane.'));
        }

        $this->response->redirect($this->helper->url->to('Project/SwimlaneController', 'index', ['project_id' => $project['id']]));
    }

    /**
     * Enable default swimlane.
     */
    public function enableDefault()
    {
        $project = $this->getProject();

        if ($this->swimlaneModel->enableDefault($project['id'])) {
            $this->flash->success(t('Swimlane updated successfully.'));
        } else {
            $this->flash->failure(t('Unable to update this swimlane.'));
        }

        $this->response->redirect($this->helper->url->to('Project/SwimlaneController', 'index', ['project_id' => $project['id']]));
    }

    /**
     * Move swimlane position.
     */
    public function move()
    {
        $project = $this->getProject();
        $values = $this->request->getJson();

        if (!empty($values) && isset($values['swimlane_id']) && isset($values['position'])) {
            $result = $this->swimlaneModel->changePosition($project['id'], $values['swimlane_id'], $values['position']);
            $this->response->json(['result' => $result]);
        } else {
            throw new AccessForbiddenException();
        }
    }

    /**
     * Get the swimlane (common method between actions).
     *
     * @throws PageNotFoundException
     *
     * @return array
     */
    protected function getSwimlane()
    {
        $swimlane = $this->swimlaneModel->getById($this->request->getIntegerParam('swimlane_id'));

        if (empty($swimlane)) {
            throw new PageNotFoundException();
        }

        return $swimlane;
    }
}
