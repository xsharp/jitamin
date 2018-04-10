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

use Jitamin\Services\Update\LatestRelease;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class of Update Service Provider.
 */
class UpdateServiceProvider implements ServiceProviderInterface
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
        $container['updateManager'] = new LatestRelease($container);

        return $container;
    }
}
