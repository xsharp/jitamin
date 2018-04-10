<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Providers;

use Jitamin\Foundation\Identity\Avatar\AvatarManager;
use Jitamin\Services\Identity\Avatar\GravatarProvider;
use Jitamin\Services\Identity\Avatar\ImageAvatarProvider;
use Jitamin\Services\Identity\Avatar\LetterAvatarProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class of Avatar Service Provider.
 */
class AvatarServiceProvider implements ServiceProviderInterface
{
    /**
     * Register providers.
     *
     * @param \Pimple\Container $container
     *
     * @return \Pimple\Container
     */
    public function register(Container $container)
    {
        $container['avatarManager'] = new AvatarManager();
        $container['avatarManager']->register(new LetterAvatarProvider($container));
        $container['avatarManager']->register(new GravatarProvider($container));
        $container['avatarManager']->register(new ImageAvatarProvider($container));

        return $container;
    }
}
