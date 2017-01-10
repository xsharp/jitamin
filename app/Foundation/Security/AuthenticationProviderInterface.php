<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Foundation\Security;

/**
 * Authentication Provider Interface.
 */
interface AuthenticationProviderInterface
{
    /**
     * Get authentication provider name.
     *
     * @return string
     */
    public function getName();

    /**
     * Authenticate the user.
     *
     * @return bool
     */
    public function authenticate();
}
