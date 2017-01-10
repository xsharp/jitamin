<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Controller\Task\Subtask;

use Jitamin\Controller\Controller;

/**
 * Subtask Status.
 */
class SubtaskStatusController extends Controller
{
    /**
     * Change status to the next status: Toto -> In Progress -> Done.
     */
    public function change()
    {
        $task = $this->getTask();
        $subtask = $this->getSubtask();

        $status = $this->subtaskStatusModel->toggleStatus($subtask['id']);

        if ($this->request->getIntegerParam('refresh-table') === 0) {
            $subtask['status'] = $status;
            $html = $this->helper->subtask->toggleStatus($subtask, $task['project_id']);
        } else {
            $html = $this->renderTable($task);
        }

        $this->response->html($html);
    }

    /**
     * Start/stop timer for subtasks.
     */
    public function timer()
    {
        $task = $this->getTask();
        $subtask_id = $this->request->getIntegerParam('subtask_id');
        $timer = $this->request->getStringParam('timer');

        if ($timer === 'start') {
            $this->subtaskTimeTrackingModel->logStartTime($subtask_id, $this->userSession->getId());
        } elseif ($timer === 'stop') {
            $this->subtaskTimeTrackingModel->logEndTime($subtask_id, $this->userSession->getId());
            $this->subtaskTimeTrackingModel->updateTaskTimeTracking($task['id']);
        }

        $this->response->html($this->renderTable($task));
    }

    /**
     * Render table.
     *
     * @param array $task
     *
     * @return string
     */
    protected function renderTable(array $task)
    {
        return $this->template->render('task/subtask/table', [
            'task'     => $task,
            'subtasks' => $this->subtaskModel->getAll($task['id']),
            'editable' => true,
        ]);
    }
}
