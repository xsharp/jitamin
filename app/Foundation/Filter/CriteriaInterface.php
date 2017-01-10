<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Foundation\Filter;

use PicoDb\Table;

/**
 * Criteria Interface.
 */
interface CriteriaInterface
{
    /**
     * Set the Query.
     *
     * @param Table $query
     *
     * @return CriteriaInterface
     */
    public function withQuery(Table $query);

    /**
     * Set filter.
     *
     * @param FilterInterface $filter
     *
     * @return CriteriaInterface
     */
    public function withFilter(FilterInterface $filter);

    /**
     * Apply condition.
     *
     * @return CriteriaInterface
     */
    public function apply();
}
