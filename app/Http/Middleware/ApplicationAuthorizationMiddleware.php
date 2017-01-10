<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Middleware;

use Jitamin\Foundation\Controller\AccessForbiddenException;
use Jitamin\Foundation\Controller\BaseMiddleware;

/**
 * Class ApplicationAuthorizationMiddleware.
 */
class ApplicationAuthorizationMiddleware extends BaseMiddleware
{
    /**
     * Execute middleware.
     */
    public function execute()
    {
        if (!$this->helper->user->hasAccess($this->router->getController(), $this->router->getAction(), $this->router->getPlugin())) {
            throw new AccessForbiddenException();
        }

        $this->next();
    }
}
