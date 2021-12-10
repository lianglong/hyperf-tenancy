<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

use Hyperf\Database\Model\Events\Saved;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Resolvers;
use Stancl\Tenancy\Resolvers\Contracts\CachedTenantResolver;

/**
 * Meant to be used on models that belong to tenants.
 */
trait InvalidatesTenantsResolverCache
{
    public static $resolvers = [
        Resolvers\DomainTenantResolver::class,
        Resolvers\PathTenantResolver::class,
        Resolvers\RequestDataTenantResolver::class,
    ];

    public static function bootInvalidatesTenantsResolverCache()
    {
//        static::saved(function (Model $model) {
//            foreach (static::$resolvers as $resolver) {
//                /** @var CachedTenantResolver $resolver */
//                $resolver = app($resolver);
//
//                $resolver->invalidateCache($model->tenant);
//            }
//        });

        $contextKey = 'tenancy.bootevents';
        $bootevents = app(\Stancl\Tenancy\CommonContainer::class)->getKey($contextKey,[]);
        $bootevents[get_called_class()]['saved'][] = function (Saved $saved){
            foreach (static::$resolvers as $resolverName) {
                /** @var CachedTenantResolver $resolver */
                $resolver = app($resolverName);

                $resolver->invalidateCache($saved->getModel()->tenant);
            }
        };
        app(\Stancl\Tenancy\CommonContainer::class)->setKey($contextKey,$bootevents);
    }

}
