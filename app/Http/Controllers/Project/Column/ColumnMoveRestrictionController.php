<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Controller\Project\Column;

use Jitamin\Controller\Controller;
use Jitamin\Foundation\Controller\AccessForbiddenException;

/**
 * Class ColumnMoveRestrictionController.
 */
class ColumnMoveRestrictionController extends Controller
{
    /**
     * Show form to create a new column restriction.
     *
     * @param array $values
     * @param array $errors
     *
     * @throws AccessForbiddenException
     */
    public function create(array $values = [], array $errors = [])
    {
        $project = $this->getProject();
        $role_id = $this->request->getIntegerParam('role_id');
        $role = $this->projectRoleModel->getById($project['id'], $role_id);

        $this->response->html($this->template->render('project/column/move_restriction/create', [
            'project' => $project,
            'role'    => $role,
            'columns' => $this->columnModel->getList($project['id']),
            'values'  => $values + ['project_id' => $project['id'], 'role_id' => $role['role_id']],
            'errors'  => $errors,
        ]));
    }

    /**
     * Save new column restriction.
     */
    public function store()
    {
        $project = $this->getProject();
        $values = $this->request->getValues();

        list($valid, $errors) = $this->columnMoveRestrictionValidator->validateCreation($values);

        if ($valid) {
            $restriction_id = $this->columnMoveRestrictionModel->create(
                $project['id'],
                $values['role_id'],
                $values['src_column_id'],
                $values['dst_column_id']
            );

            if ($restriction_id !== false) {
                $this->flash->success(t('The column restriction has been created successfully.'));
            } else {
                $this->flash->failure(t('Unable to create this column restriction.'));
            }

            $this->response->redirect($this->helper->url->to('Task/ProjectRoleController', 'show', ['project_id' => $project['id']]));
        } else {
            $this->create($values, $errors);
        }
    }

    /**
     * Remove a restriction.
     */
    public function remove()
    {
        $project = $this->getProject();
        $restriction_id = $this->request->getIntegerParam('restriction_id');

        if ($this->request->isPost()) {
            $this->request->checkCSRFToken();
            if ($this->columnMoveRestrictionModel->remove($restriction_id)) {
                $this->flash->success(t('Column restriction removed successfully.'));
            } else {
                $this->flash->failure(t('Unable to remove this restriction.'));
            }

            return $this->response->redirect($this->helper->url->to('Task/ProjectRoleController', 'show', ['project_id' => $project['id']]));
        }

        return $this->response->html($this->helper->layout->project('project/column/move_restriction/remove', [
            'project'     => $project,
            'restriction' => $this->columnMoveRestrictionModel->getById($project['id'], $restriction_id),
        ]));
    }
}
