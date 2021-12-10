<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Middleware;

use Closure;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Stancl\Tenancy\Tenancy;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class InitializeTenancyByDomain extends IdentificationMiddleware implements MiddlewareInterface
{
    /** @var callable|null */
    public static $onFail;

    /** @var DomainTenantResolver */
    protected $resolver;

    public function __construct(DomainTenantResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Handle an incoming request.
     *
     * @param  ServerRequestInterface  $request
     * @param  RequestHandlerInterface $handler
     * @return mixed
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->initializeTenancy(
            $request, $handler, $request->getUri()->getHost()
        );
    }
}
