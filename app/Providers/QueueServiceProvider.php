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

use Jitamin\Foundation\Queue\QueueManager;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class of Queue Service Provider.
 */
class QueueServiceProvider implements ServiceProviderInterface
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
        $container['queueManager'] = new QueueManager($container);

        return $container;
    }
}
