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
 * Task helpers.
 */
class TaskHelper extends Base
{
    /**
     * Local cache for project columns.
     *
     * @var array
     */
    private $columns = [];

    /**
     * Get the color list.
     *
     * @return array
     */
    public function getColors()
    {
        return $this->colorModel->getList();
    }

    /**
     * Return the list recurrence triggers.
     *
     * @return array
     */
    public function recurrenceTriggers()
    {
        return $this->taskRecurrenceModel->getRecurrenceTriggerList();
    }

    /**
     * Return the list recurrence timeframes.
     *
     * @return array
     */
    public function recurrenceTimeframes()
    {
        return $this->taskRecurrenceModel->getRecurrenceTimeframeList();
    }

    /**
     * Return the list options to calculate recurrence due date.
     *
     * @return array
     */
    public function recurrenceBasedates()
    {
        return $this->taskRecurrenceModel->getRecurrenceBasedateList();
    }

    /**
     * Display a select field of title.
     *
     * @param array $values Form values
     * @param array $errors Form errors
     *
     * @return string
     */
    public function selectTitle(array $values, array $errors)
    {
        $html = $this->helper->form->label(t('Title'), 'title');
        $html .= $this->helper->form->text('title', $values, $errors, ['autofocus', 'required', 'maxlength="200"', 'tabindex="1"'], 'form-input-large');

        return $html;
    }

    /**
     * Display a select field of description.
     *
     * @param array $values Form values
     * @param array $errors Form errors
     *
     * @return string
     */
    public function selectDescription(array $values, array $errors)
    {
        $html = $this->helper->form->label(t('Description'), 'description');
        $html .= $this->helper->form->textEditor('description', $values, $errors, ['tabindex' => 2, 'placeholder' => t('Please add descriptive text to help others better understand this task')]);

        return $html;
    }

    /**
     * Display a select field of tags.
     *
     * @param array $values Form values
     * @param array $targs  Form tags
     *
     * @return string
     */
    public function selectTags(array $project, array $tags = [])
    {
        $options = $this->tagModel->getAssignableList($project['id']);

        $html = $this->helper->form->label(t('Tags'), 'tags[]');
        $html .= '<input type="hidden" name="tags[]" value="">';
        $html .= '<select name="tags[]" id="form-tags" class="tag-autocomplete" multiple>';

        foreach ($options as $tag) {
            $html .= sprintf(
                '<option value="%s" %s>%s</option>',
                $this->helper->text->e($tag),
                in_array($tag, $tags) ? 'selected="selected"' : '',
                $this->helper->text->e($tag)
            );
        }

        $html .= '</select>';

        return $html;
    }

    /**
     * Display a select field of color.
     *
     * @param array $values Form values
     *
     * @return string
     */
    public function selectColor(array $values)
    {
        $colors = $this->colorModel->getList();
        $html = $this->helper->form->label(t('Color'), 'color_id');
        $html .= $this->helper->form->select('color_id', $colors, $values, [], [], 'color-picker');

        return $html;
    }

    /**
     * Display a select field of project.
     *
     * @param array $projects
     * @param array $values     Form values
     * @param array $errors     Form errors
     * @param array $attributes
     *
     * @return string
     */
    public function selectProject(array $projects, array $values, array $errors = [], array $attributes = [])
    {
        $attributes = array_merge(['tabindex="2"'], $attributes);

        $html = $this->helper->form->label(t('Project'), 'project_id');
        $html .= $this->helper->form->select('project_id', $projects, $values, $errors, $attributes);

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
        $attributes = array_merge(['tabindex="3"'], $attributes);

        $html = $this->helper->form->label(t('Assignee'), 'owner_id');
        $html .= $this->helper->form->select('owner_id', $users, $values, $errors, $attributes);
        $html .= '&nbsp;';
        $html .= '<small>';
        $html .= '<a href="#" class="assign-me" data-target-id="form-owner_id" data-current-id="'.$this->userSession->getId().'" title="'.t('Assign to me').'">'.t('Me').'</a>';
        $html .= '</small>';

        return $html;
    }

    /**
     * Display a select field of category.
     *
     * @param array $categories
     * @param array $values     Form values
     * @param array $errors     Form errors
     * @param array $attributes
     * @param bool allow_one_item
     *
     * @return string
     */
    public function selectCategory(array $categories, array $values, array $errors = [], array $attributes = [], $allow_one_item = false)
    {
        $attributes = array_merge(['tabindex="4"'], $attributes);
        $html = '';

        if (!(!$allow_one_item && count($categories) === 1 && key($categories) == 0)) {
            $html .= $this->helper->form->label(t('Category'), 'category_id');
            $html .= $this->helper->form->select('category_id', $categories, $values, $errors, $attributes);
        }

        return $html;
    }

    /**
     * Display a select field of swimlane.
     *
     * @param array $swimlanes
     * @param array $values     Form values
     * @param array $errors     Form errors
     * @param array $attributes
     *
     * @return string
     */
    public function selectSwimlane(array $swimlanes, array $values, array $errors = [], array $attributes = [])
    {
        $attributes = array_merge(['tabindex="5"'], $attributes);
        $html = '';

        if (!(count($swimlanes) === 1 && key($swimlanes) == 0)) {
            $html .= $this->helper->form->label(t('Swimlane'), 'swimlane_id');
            $html .= $this->helper->form->select('swimlane_id', $swimlanes, $values, $errors, $attributes);
        }

        return $html;
    }

    /**
     * Display a select field of column.
     *
     * @param array $columns
     * @param array $values     Form values
     * @param array $errors     Form errors
     * @param array $attributes
     *
     * @return string
     */
    public function selectColumn(array $columns, array $values, array $errors = [], array $attributes = [])
    {
        $attributes = array_merge(['tabindex="6"'], $attributes);

        $html = $this->helper->form->label(t('Column'), 'column_id');
        $html .= $this->helper->form->select('column_id', $columns, $values, $errors, $attributes);

        return $html;
    }

    /**
     * Display a select field of column.
     *
     * @param array $project
     * @param array $values  Form values
     *
     * @return string
     */
    public function selectPriority(array $project, array $values)
    {
        $html = '';

        if ($project['priority_end'] != $project['priority_start']) {
            $range = range($project['priority_end'], $project['priority_start']);
            $options = array_combine($range, $range);
            array_walk($options, create_function('&$val', '$val = t(\'P\'.$val);'));
            $values += ['priority' => $project['priority_default']];

            $html .= $this->helper->form->label(t('Priority'), 'priority');
            $html .= $this->helper->form->select('priority', $options, $values, [], ['tabindex="7"'], 'priority-picker');
        }

        return $html;
    }

    /**
     * Display a select field of score.
     *
     * @param array $values     Form values
     * @param array $errors     Form errors
     * @param array $attributes
     *
     * @return string
     */
    public function selectScore(array $values, array $errors = [], array $attributes = [])
    {
        $attributes = array_merge(['tabindex="8"'], $attributes);

        $html = $this->helper->form->label(t('Complexity'), 'score');
        $html .= $this->helper->form->number('score', $values, $errors, $attributes);

        return $html;
    }

    /**
     * Display a select field of reference.
     *
     * @param array $values     Form values
     * @param array $errors     Form errors
     * @param array $attributes
     *
     * @return string
     */
    public function selectReference(array $values, array $errors = [], array $attributes = [])
    {
        $attributes = array_merge(['tabindex="9"'], $attributes);

        $html = $this->helper->form->label(t('Reference'), 'reference');
        $html .= $this->helper->form->text('reference', $values, $errors, $attributes, 'form-input-small');

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
        $attributes = array_merge(['tabindex="10"'], $attributes);

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
        $attributes = array_merge(['tabindex="11"'], $attributes);

        $html = $this->helper->form->label(t('Time spent'), 'time_spent');
        $html .= $this->helper->form->numeric('time_spent', $values, $errors, $attributes);
        $html .= ' '.t('hours');

        return $html;
    }

    /**
     * Display a select field of start date.
     *
     * @param array $values     Form values
     * @param array $errors     Form errors
     * @param array $attributes
     *
     * @return string
     */
    public function selectStartDate(array $values, array $errors = [], array $attributes = [])
    {
        $attributes = array_merge(['tabindex="12"'], $attributes);

        return $this->helper->form->datetime(t('Start Date'), 'date_started', $values, $errors, $attributes);
    }

    /**
     * Display a select field of due date.
     *
     * @param array $values     Form values
     * @param array $errors     Form errors
     * @param array $attributes
     *
     * @return string
     */
    public function selectDueDate(array $values, array $errors = [], array $attributes = [])
    {
        $attributes = array_merge(['tabindex="13"'], $attributes);

        return $this->helper->form->date(t('Due Date'), 'date_due', $values, $errors, $attributes);
    }

    /**
     * Display a select field of progress.
     *
     * @param array $values     Form values
     * @param array $errors     Form errors
     * @param array $attributes
     *
     * @return string
     */
    public function selectProgress(array $values, array $errors = [], array $attributes = [])
    {
        $attributes = array_merge(['tabindex="14"'], $attributes);

        $html = $this->helper->form->label(t('Progress'), 'progress');
        $html .= $this->helper->form->number('progress', $values, $errors, $attributes);

        $html .= '&nbsp;';
        $html .= '<small>%</small>';

        return $html;
    }

    /**
     * Format the priority.
     *
     * @param array $project
     * @param array $task
     *
     * @return string
     */
    public function formatPriority(array $project, array $task)
    {
        $html = '';

        if ($project['priority_end'] != $project['priority_start']) {
            $html .= '<span class="task-board-priority" title="'.t('Task priority').'">';
            $html .= $task['priority'] = t('P'.$task['priority']);
            $html .= '</span>';
        }

        return $html;
    }

    /**
     * Returns the task progress.
     *
     * @param array $task
     *
     * @return int
     */
    public function getProgress($task)
    {
        if (!isset($this->columns[$task['project_id']])) {
            $this->columns[$task['project_id']] = $this->columnModel->getList($task['project_id']);
        }

        return $this->taskModel->getProgress($task, $this->columns[$task['project_id']]);
    }
}
