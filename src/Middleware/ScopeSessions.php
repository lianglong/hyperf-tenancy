<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Middleware;

use Closure;
//use Illuminate\Http\Request;
use Hyperf\HttpMessage\Exception\HttpException;
use Stancl\Tenancy\Exceptions\TenancyNotInitializedException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

class ScopeSessions implements MiddlewareInterface
{
    public static $tenantIdKey = '_tenant_id';

    /**
     * @var \Hyperf\Contract\SessionInterface
     */
    private $session;

    public function __construct(\Hyperf\Contract\SessionInterface $session)
    {
        $this->session = $session;
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
        if (! tenancy()->initialized) {
            throw new TenancyNotInitializedException('Tenancy needs to be initialized before the session scoping middleware is executed');
        }

        if (! $this->session->has(static::$tenantIdKey)) {
            $this->session->set(static::$tenantIdKey, tenant()->getTenantKey());
        } else {
            if ($this->session->get(static::$tenantIdKey) !== tenant()->getTenantKey()) {
//                abort(403);
                throw new HttpException(403);
            }
        }

        return $handler->handle($request);
    }
}
