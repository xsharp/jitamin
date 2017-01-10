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
use Jitamin\Model\ProjectActivityModel;

/**
 * Filter activity events by taskId.
 */
class ProjectActivityTaskIdFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute.
     *
     * @return string[]
     */
    public function getAttributes()
    {
        return ['task_id'];
    }

    /**
     * Apply filter.
     *
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->eq(ProjectActivityModel::TABLE.'.task_id', $this->value);

        return $this;
    }
}
