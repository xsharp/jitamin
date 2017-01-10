<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Services\Identity\Avatar;

use Jitamin\Foundation\Base;
use Jitamin\Foundation\Identity\Avatar\AvatarProviderInterface;

/**
 * Gravatar Avatar Provider.
 */
class GravatarProvider extends Base implements AvatarProviderInterface
{
    /**
     * Render avatar html.
     *
     * @param array $user
     * @param int   $size
     *
     * @return string
     */
    public function render(array $user, $size)
    {
        $url = sprintf('https://www.gravatar.com/avatar/%s?s=%d', md5(strtolower($user['email'])), $size);
        $title = $this->helper->text->e($user['name'] ?: $user['username']);

        return '<img src="'.$url.'" alt="'.$title.'" title="'.$title.'">';
    }

    /**
     * Determine if the provider is active.
     *
     * @param array $user
     *
     * @return bool
     */
    public function isActive(array $user)
    {
        return !empty($user['email']) && $this->settingModel->get('integration_gravatar') == 1;
    }
}
