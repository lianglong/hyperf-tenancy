<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Middleware;

use Closure;
//use Illuminate\Http\Request;
//use Illuminate\Routing\Route;
use Stancl\Tenancy\Exceptions\RouteIsMissingTenantParameterException;
use Stancl\Tenancy\Resolvers\PathTenantResolver;
use Stancl\Tenancy\Tenancy;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

class InitializeTenancyByPath extends IdentificationMiddleware implements MiddlewareInterface
{
    /** @var callable|null */
    public static $onFail;

    /** @var PathTenantResolver */
    protected $resolver;

    public function __construct(PathTenantResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
//        /** @var Route $route */
//        $route = $request->route();

        // Only initialize tenancy if tenant is the first parameter
        // We don't want to initialize tenancy if the tenant is
        // simply injected into some route controller action.
        if ( ($tenant = $request->route(PathTenantResolver::$tenantParameterName)) != null ) {
            return $this->initializeTenancy(
                $request, $handler, $tenant
            );
        } else {
            throw new RouteIsMissingTenantParameterException;
        }

        return $handler->handle($request);
    }
}
