<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Bootstrappers;

use Illuminate\Cache\CacheManager;
//use Illuminate\Contracts\Foundation\Application;
use Psr\Container\ContainerInterface;
//use Illuminate\Support\Facades\Cache;
use Stancl\Tenancy\CacheManager as TenantCacheManager;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class CacheTenancyBootstrapper implements TenancyBootstrapper
{
    /** @var CacheManager */
    protected $originalCache;

    /** @var ContainerInterface */
    protected $app;

    public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
    }

    public function bootstrap(Tenant $tenant)
    {
//        $this->resetFacadeCache();

        $this->originalCache = $this->originalCache ?? CacheManager::class;//$this->app->get(CacheManager::class);
//        $this->app->set(CacheManager::class,make(TenantCacheManager::class));
        $this->app->define(CacheManager::class,TenantCacheManager::class);
//        $this->app->extend('cache', function () {
//            return new TenantCacheManager($this->app);
//        });
    }

    public function revert()
    {
//        $this->resetFacadeCache();

//        $this->app->extend('cache', function () {
//            return $this->originalCache;
//        });
        $this->app->define(CacheManager::class,$this->originalCache);

        $this->originalCache = null;
    }

    /**
     * This wouldn't be necessary, but is needed when a call to the
     * facade has been made prior to bootstrapping tenancy. The
     * facade has its own cache, separate from the container.
     */
    public function resetFacadeCache()
    {
//        Cache::clearResolvedInstances();
    }
}
