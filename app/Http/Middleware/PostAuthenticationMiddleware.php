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

use Jitamin\Foundation\Controller\BaseMiddleware;

/**
 * Class PostAuthenticationMiddleware.
 */
class PostAuthenticationMiddleware extends BaseMiddleware
{
    /**
     * Execute middleware.
     */
    public function execute()
    {
        $controller = strtolower($this->router->getController());
        $action = strtolower($this->router->getAction());
        $ignore = ($controller === 'profile/twofactorcontroller' && in_array($action, ['code', 'check'])) || ($controller === 'auth/authcontroller' && $action === 'logout');

        if ($ignore === false && $this->userSession->hasPostAuthentication() && !$this->userSession->isPostAuthenticationValidated()) {
            $this->nextMiddleware = null;

            if ($this->request->isAjax()) {
                $this->response->text('Not Authorized', 401);
            } else {
                $this->response->redirect($this->helper->url->to('Profile/TwoFactorController', 'code'));
            }
        }

        $this->next();
    }
}
