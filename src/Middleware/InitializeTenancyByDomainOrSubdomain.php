<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Middleware;

use Closure;
use Hyperf\Utils\Str;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class InitializeTenancyByDomainOrSubdomain implements MiddlewareInterface
{

    /**
     * Handle an incoming request.
     *
     * @param  ServerRequestInterface  $request
     * @param  RequestHandlerInterface $handler
     * @return mixed
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isSubdomain($request->getUri()->getHost())) {
            return app(InitializeTenancyBySubdomain::class)->handle($request, $handler);
        } else {
            return app(InitializeTenancyByDomain::class)->handle($request, $handler);
        }
    }

    protected function isSubdomain(string $hostname): bool
    {
        return Str::endsWith($hostname, config('tenancy.central_domains'));
    }
}
