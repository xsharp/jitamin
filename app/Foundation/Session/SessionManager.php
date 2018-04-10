<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Foundation\Session;

use Jitamin\Foundation\Base;

/**
 * Session Manager.
 */
class SessionManager extends Base
{
    /**
     * Event names.
     *
     * @var string
     */
    const EVENT_DESTROY = 'session.destroy';

    /**
     * Return true if the session is open.
     *
     * @static
     *
     * @return bool
     */
    public static function isOpen()
    {
        return session_id() !== '';
    }

    /**
     * Create a new session.
     */
    public function open()
    {
        $this->configure();

        if (ini_get('session.auto_start') == 1) {
            session_destroy();
        }

        session_name('JM_SID');
        session_start();

        $this->sessionStorage->setStorage($_SESSION);
    }

    /**
     * Destroy the session.
     */
    public function close()
    {
        $this->dispatcher->dispatch(self::EVENT_DESTROY);

        // Destroy the session cookie
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );

        session_unset();
        session_destroy();
    }

    /**
     * Define session settings.
     */
    private function configure()
    {
        // Session cookie: HttpOnly and secure flags
        session_set_cookie_params(
            SESSION_DURATION,
            $this->helper->url->dir() ?: '/',
            null,
            $this->request->isHTTPS(),
            true
        );

        // Avoid session id in the URL
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');

        // Enable strict mode
        ini_set('session.use_strict_mode', '1');

        // Better session hash
        ini_set('session.hash_function', '1'); // 'sha512' is not compatible with FreeBSD, only MD5 '0' and SHA-1 '1' seems to work
        ini_set('session.hash_bits_per_character', 6);

        // Set an additional entropy
        ini_set('session.entropy_file', '/dev/urandom');
        ini_set('session.entropy_length', '256');
    }
}
