<?php

declare(strict_types=1);

use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Tenancy;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;
use Psr\EventDispatcher\EventDispatcherInterface;

if (! function_exists('app')) {
    /** @return ApplicationContext */
    function app(string $abstract = null, array $parameters = [])
    {
        if( $abstract == null )
        {
            return ApplicationContext::getContainer();
        }
        if( ApplicationContext::getContainer()->has($abstract) )
        {
            return ApplicationContext::getContainer()->get($abstract);
        }
        return ApplicationContext::getContainer()->make($abstract,$parameters);
    }
}


if ( !function_exists('event') ){
    /**
     * 兼容laravel的事件触发
     * @param mixed ...$args
     * @return object
     */
    function event(...$args)
    {
        return app(EventDispatcherInterface::class)->dispatch(...$args);
    }
}

if (! function_exists('tenancy')) {
    /** @return Tenancy */
    function tenancy()
    {
        if( !Context::has(Tenancy::class) ){
            Context::set(Tenancy::class,make(Tenancy::class));
        }
        return Context::get(Tenancy::class);
    }
}

if (! function_exists('tenant')) {
    /**
     * Get a key from the current tenant's storage.
     *
     * @param string|null $key
     * @return Tenant|null|mixed
     */
    function tenant($key = null)
    {
        $tenant = null;
        if( ! \tenancy()->initialized ){
            $tenant = make(config('tenancy.tenant_model'));
        }else{
            $tenant = \tenancy()->tenant;
        }

        if (is_null($key)) {
            return $tenant;
        }
        return optional($tenant)->getAttribute($key) ?? null;
    }
}

if( !function_exists('tenant_wait') ){
    function tenant_wait(Closure $closure, ?float $timeout = null)
    {
        if( tenancy()->initialized ){
            $tenant = tenancy()->tenant;
            $closure = function () use ($closure,$tenant){
                tenancy()->initialize($tenant);
                return call($closure);
            };
        }
        return wait($closure,$timeout);
    }
}

if( !function_exists('tenant_go') ){
    function tenant_go(callable $callable)
    {
        if( tenancy()->initialized ){
            $tenant = tenancy()->tenant;
            $callable = function () use ($callable,$tenant){
                tenancy()->initialize($tenant);
                call($callable);
            };
        }
        return go($callable);
    }
}
