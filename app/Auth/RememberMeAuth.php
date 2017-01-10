<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Auth;

use Jitamin\Foundation\Base;
use Jitamin\Foundation\Security\PreAuthenticationProviderInterface;
use Jitamin\Services\Identity\DatabaseUserProvider;

/**
 * Rember Me Cookie Authentication Provider.
 */
class RememberMeAuth extends Base implements PreAuthenticationProviderInterface
{
    /**
     * User properties.
     *
     * @var array
     */
    protected $userInfo = [];

    /**
     * Get authentication provider name.
     *
     * @return string
     */
    public function getName()
    {
        return 'RememberMe';
    }

    /**
     * Authenticate the user.
     *
     * @return bool
     */
    public function authenticate()
    {
        $credentials = $this->rememberMeCookie->read();

        if ($credentials !== false) {
            $session = $this->rememberMeSessionModel->find($credentials['token'], $credentials['sequence']);

            if (!empty($session)) {
                $this->rememberMeCookie->write(
                    $session['token'],
                    $this->rememberMeSessionModel->updateSequence($session['token']),
                    $session['expiration']
                );

                $this->userInfo = $this->userModel->getById($session['user_id']);

                return true;
            }
        }

        return false;
    }

    /**
     * Get user object.
     *
     * @return DatabaseUserProvider
     */
    public function getUser()
    {
        if (empty($this->userInfo)) {
            return;
        }

        return new DatabaseUserProvider($this->userInfo);
    }
}
