<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Middleware;

use Closure;
//use Illuminate\Http\Request;
use Hyperf\HttpMessage\Exception\HttpException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

class PreventAccessFromCentralDomains implements MiddlewareInterface
{
    /**
     * Set this property if you want to customize the on-fail behavior.
     *
     * @var callable|null
     */
    public static $abortRequest;

    /**
     * Handle an incoming request.
     *
     * @param  ServerRequestInterface  $request
     * @param  RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (in_array($request->getUri()->getHost(), config('tenancy.central_domains'))) {
            $abortRequest = static::$abortRequest ?? function () {
//                abort(404);
                throw new HttpException(404);
            };

            return $abortRequest($request, $handler);
        }

        return $handler->handle($request);
    }
}
