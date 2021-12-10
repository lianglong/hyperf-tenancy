<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Resolvers\RequestDataTenantResolver;
use Stancl\Tenancy\Tenancy;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

class InitializeTenancyByRequestData extends IdentificationMiddleware implements MiddlewareInterface
{
    /** @var string|null */
    public static $header = 'X-Tenant';

    /** @var string|null */
    public static $queryParameter = 'tenant';

    /** @var callable|null */
    public static $onFail;

    /** @var RequestDataTenantResolver */
    protected $resolver;

    public function __construct( RequestDataTenantResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Handle an incoming request.
     *
     * @param  ServerRequestInterface  $request
     * @param  RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() !== 'OPTIONS') {
            return $this->initializeTenancy($request, $handler, $this->getPayload($request));
        }

        return $handler->handle($request);
    }

    protected function getPayload(ServerRequestInterface $request): ?string
    {
        $tenant = null;
        if (static::$header && $request->hasHeader(static::$header)) {
            $tenant = $request->getHeaderLine(static::$header);
        } elseif (static::$queryParameter && $request->has(static::$queryParameter)) {
            $tenant = $request->get(static::$queryParameter);
        }

        return $tenant;
    }
}
