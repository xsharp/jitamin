<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Filter;

use Jitamin\Foundation\Filter\FilterInterface;
use Jitamin\Model\TaskModel;

/**
 * Filter tasks by due date range.
 */
class TaskDueDateRangeFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute.
     *
     * @return string[]
     */
    public function getAttributes()
    {
        return [];
    }

    /**
     * Apply filter.
     *
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->gte(TaskModel::TABLE.'.date_due', is_numeric($this->value[0]) ? $this->value[0] : strtotime($this->value[0]));
        $this->query->lte(TaskModel::TABLE.'.date_due', is_numeric($this->value[1]) ? $this->value[1] : strtotime($this->value[1]));

        return $this;
    }
}
