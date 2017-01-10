<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Policy;

use Jitamin\Foundation\Base;
use JsonRPC\Exception\AccessDeniedException;

/**
 * Class UserPolicy.
 */
class UserPolicy extends Base
{
    /**
     * Determine if the current user has permissions.
     *
     * @param string $class
     * @param string $method
     *
     * @throws \JsonRPC\Exception\AccessDeniedException
     */
    public function check($class, $method)
    {
        if ($this->userSession->isLogged() && !$this->apiAuthorization->isAllowed($class, $method, $this->userSession->getRole())) {
            throw new AccessDeniedException('You are not allowed to access to this resource');
        }
    }
}
