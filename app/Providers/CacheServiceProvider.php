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

use Jitamin\Decorator\ColumnMoveRestrictionCacheDecorator;
use Jitamin\Decorator\ColumnRestrictionCacheDecorator;
use Jitamin\Decorator\MetadataCacheDecorator;
use Jitamin\Decorator\ProjectRoleRestrictionCacheDecorator;
use Jitamin\Foundation\Cache\FileCache;
use Jitamin\Foundation\Cache\MemcachedCache;
use Jitamin\Foundation\Cache\MemoryCache;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class of Cache Service Provider.
 */
class CacheServiceProvider implements ServiceProviderInterface
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
        $container['memoryCache'] = function () {
            return new MemoryCache();
        };

        if (CACHE_DRIVER === 'file') {
            $container['cacheDriver'] = function () {
                return new FileCache();
            };
        } elseif (CACHE_DRIVER === 'memcached') {
            $container['cacheDriver'] = function ($c) {
                return new MemcachedCache($c['memcached'], defined('CACHE_PREFIX') ? CACHE_PREFIX : '');
            };
        } else {
            $container['cacheDriver'] = $container['memoryCache'];
        }

        $container['userMetadataCacheDecorator'] = function ($c) {
            return new MetadataCacheDecorator(
                $c['cacheDriver'],
                $c['userMetadataModel'],
                'user.metadata.',
                $c['userSession']->getId()
            );
        };

        $container['columnMoveRestrictionCacheDecorator'] = function ($c) {
            return new ColumnMoveRestrictionCacheDecorator(
                $c['memoryCache'],
                $c['columnMoveRestrictionModel']
            );
        };

        $container['columnRestrictionCacheDecorator'] = function ($c) {
            return new ColumnRestrictionCacheDecorator(
                $c['memoryCache'],
                $c['columnRestrictionModel']
            );
        };

        $container['projectRoleRestrictionCacheDecorator'] = function ($c) {
            return new ProjectRoleRestrictionCacheDecorator(
                $c['memoryCache'],
                $c['projectRoleRestrictionModel']
            );
        };

        return $container;
    }
}
