<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Middleware;

use Stancl\Tenancy\Contracts\TenantCouldNotBeIdentifiedException;
use Stancl\Tenancy\Contracts\TenantResolver;
use Stancl\Tenancy\Tenancy;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class IdentificationMiddleware
{
    /** @var callable */
    public static $onFail;

    /** @var TenantResolver */
    protected $resolver;

    public function initializeTenancy(ServerRequestInterface $request, RequestHandlerInterface $handler, ...$resolverArguments)
    {
        try {
            tenancy()->initialize(
                $this->resolver->resolve(...$resolverArguments)
            );
        } catch (TenantCouldNotBeIdentifiedException $e) {
            $onFail = static::$onFail ?? function ($e) {
                throw $e;
            };

            return $onFail($e, $request, $handler);
        }

        return $handler->handle($request);
    }
}
