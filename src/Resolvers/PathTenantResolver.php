<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Resolvers;

//use Illuminate\Routing\Route;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedByPathException;

class PathTenantResolver extends Contracts\CachedTenantResolver
{
    public static $tenantParameterName = 'tenant';

    /** @var bool */
    public static $shouldCache = false;

    /** @var int */
    public static $cacheTTL = 3600; // seconds

    /** @var string|null */
    public static $cacheStore = null; // default

    public function resolveWithoutCache(...$args): Tenant
    {
//        /** @var Route $route */
        $id = $args[0];

//        if ($id = $route->parameter(static::$tenantParameterName)) {
//            $route->forgetParameter(static::$tenantParameterName);
//            if ($tenant = tenancy()->find($id)) {
//                return $tenant;
//            }
//        }
        if ($id && $tenant = tenancy()->find($id)) {
            return $tenant;
        }

        throw new TenantCouldNotBeIdentifiedByPathException($id);
    }

    public function getArgsForTenant(Tenant $tenant): array
    {
        return [
            [$tenant->{$tenant->getTenantKeyName()}],
        ];
    }
}
