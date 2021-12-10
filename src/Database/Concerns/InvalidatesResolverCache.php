<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

use Hyperf\Database\Model\Events\Deleted;
use Hyperf\Database\Model\Events\Saved;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Resolvers;
use Stancl\Tenancy\Resolvers\Contracts\CachedTenantResolver;

trait InvalidatesResolverCache
{
    public static $resolvers = [
        Resolvers\DomainTenantResolver::class,
        Resolvers\PathTenantResolver::class,
        Resolvers\RequestDataTenantResolver::class,
    ];

    public static function bootInvalidatesResolverCache()
    {
//        static::saved(function (Tenant $tenant) {
//            foreach (static::$resolvers as $resolver) {
//                /** @var CachedTenantResolver $resolver */
//                $resolver = app($resolver);
//
//                $resolver->invalidateCache($tenant);
//            }
//        });
        $contextKey = 'tenancy.bootevents';
        $bootevents = app(\Stancl\Tenancy\CommonContainer::class)->getKey($contextKey,[]);
        $bootevents[get_called_class()]['saved'][] = function (Saved $saved){
            foreach (static::$resolvers as $resolverName) {
                /** @var CachedTenantResolver $resolver */
                $resolver = app($resolverName);

                $resolver->invalidateCache($saved->getModel());
            }
        };

        $bootevents[get_called_class()]['deleted'][] = function (Deleted $deleted){
            foreach (static::$resolvers as $resolverName) {
                /** @var CachedTenantResolver $resolver */
                $resolver = app($resolverName);

                $resolver->invalidateCache($deleted->getModel());
            }
        };


        app(\Stancl\Tenancy\CommonContainer::class)->setKey($contextKey,$bootevents);
    }

}
