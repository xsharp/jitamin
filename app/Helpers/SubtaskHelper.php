<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Helper;

use Jitamin\Foundation\Base;

/**
 * Subtask helpers.
 */
class SubtaskHelper extends Base
{
    /**
     * Get the title.
     *
     * @param array $subtask
     *
     * @return string
     */
    public function getTitle(array $subtask)
    {
        if ($subtask['status'] == 0) {
            $html = '<i class="fa fa-square-o fa-fw" title="'.t('Backlog').'"></i>';
        } elseif ($subtask['status'] == 1) {
            $html = '<i class="fa fa-caret-square-o-right fa-fw" title="'.t('Work in progress').'"></i>';
        } else {
            $html = '<i class="fa fa-check-square-o fa-fw" title="'.t('Done').'"></i>';
        }

        return $html.$this->helper->text->e($subtask['title']);
    }

    /**
     * Get the link to toggle subtask status.
     *
     * @param array $subtask
     * @param int   $project_id
     * @param bool  $refresh_table
     *
     * @return string
     */
    public function toggleStatus(array $subtask, $project_id, $refresh_table = false)
    {
        if (!$this->helper->user->hasProjectAccess('Task/Subtask/SubtaskController', 'edit', $project_id)) {
            return $this->getTitle($subtask);
        }

        $params = ['task_id' => $subtask['task_id'], 'subtask_id' => $subtask['id'], 'refresh-table' => (int) $refresh_table];

        if ($subtask['status'] == 0 && isset($this->sessionStorage->hasSubtaskInProgress) && $this->sessionStorage->hasSubtaskInProgress) {
            return $this->helper->url->link($this->getTitle($subtask), 'Task/Subtask/SubtaskRestrictionController', 'show', $params, false, 'popover');
        }

        $class = 'subtask-toggle-status '.($refresh_table ? 'subtask-refresh-table' : '');

        return $this->helper->url->link($this->getTitle($subtask), 'Task/Subtask/SubtaskStatusController', 'change', $params, false, $class);
    }

    /**
     * Display a select field of title.
     *
     * @param array $values     Form values
     * @param array $errors     Form errors
     * @param array $attributes
     *
     * @return string
     */
    public function selectTitle(array $values, array $errors = [], array $attributes = [])
    {
        $attributes = array_merge(['tabindex="1"', 'required', 'maxlength="255"'], $attributes);

        $html = $this->helper->form->label(t('Title'), 'title');
        $html .= $this->helper->form->text('title', $values, $errors, $attributes);

        return $html;
    }

    /**
     * Display a select field of assignee.
     *
     * @param array $users
     * @param array $values     Form values
     * @param array $errors     Form errors
     * @param array $attributes
     *
     * @return string
     */
    public function selectAssignee(array $users, array $values, array $errors = [], array $attributes = [])
    {
        $attributes = array_merge(['tabindex="2"'], $attributes);

        $html = $this->helper->form->label(t('Assignee'), 'user_id');
        $html .= $this->helper->form->select('user_id', $users, $values, $errors, $attributes);
        $html .= '&nbsp;';
        $html .= '<small>';
        $html .= '<a href="#" class="assign-me" data-target-id="form-user_id" data-current-id="'.$this->userSession->getId().'" title="'.t('Assign to me').'">'.t('Me').'</a>';
        $html .= '</small>';

        return $html;
    }

    /**
     * Display a select field of time estimated.
     *
     * @param array $values     Form values
     * @param array $errors     Form errors
     * @param array $attributes
     *
     * @return string
     */
    public function selectTimeEstimated(array $values, array $errors = [], array $attributes = [])
    {
        $attributes = array_merge(['tabindex="3"'], $attributes);

        $html = $this->helper->form->label(t('Original estimate'), 'time_estimated');
        $html .= $this->helper->form->numeric('time_estimated', $values, $errors, $attributes);
        $html .= ' '.t('hours');

        return $html;
    }

    /**
     * Display a select field of time spent.
     *
     * @param array $values     Form values
     * @param array $errors     Form errors
     * @param array $attributes
     *
     * @return string
     */
    public function selectTimeSpent(array $values, array $errors = [], array $attributes = [])
    {
        $attributes = array_merge(['tabindex="4"'], $attributes);

        $html = $this->helper->form->label(t('Time spent'), 'time_spent');
        $html .= $this->helper->form->numeric('time_spent', $values, $errors, $attributes);
        $html .= ' '.t('hours');

        return $html;
    }
}
