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
 * Filter Interface.
 */
interface FilterInterface
{
    /**
     * BaseFilter constructor.
     *
     * @param mixed $value
     */
    public function __construct($value = null);

    /**
     * Set the value.
     *
     * @param string $value
     *
     * @return FilterInterface
     */
    public function withValue($value);

    /**
     * Set query.
     *
     * @param Table $query
     *
     * @return FilterInterface
     */
    public function withQuery(Table $query);

    /**
     * Get search attribute.
     *
     * @return string[]
     */
    public function getAttributes();

    /**
     * Apply filter.
     *
     * @return FilterInterface
     */
    public function apply();
}
