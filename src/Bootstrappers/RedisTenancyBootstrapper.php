<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Bootstrappers;

//use Illuminate\Contracts\Config\Repository;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Redis\Redis;
use Psr\Container\ContainerInterface;
//use Illuminate\Support\Facades\Redis;
use Hyperf\Redis\RedisFactory;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class RedisTenancyBootstrapper implements TenancyBootstrapper
{
    /** @var array<string, string> Original prefixes of connections */
    public $originalPrefixes = [];

    /** @var ConfigInterface */
    protected $config;

    /** @var ContainerInterface */
    protected $app;

    public function __construct(ContainerInterface $app,ConfigInterface $config)
    {
        $this->config = $config;
        $this->app = $app;
    }

    public function bootstrap(Tenant $tenant)
    {
        foreach ($this->prefixedConnections() as $connection) {
            $prefix = $this->config->get('tenancy.redis.prefix_base') . $tenant->getTenantKey();
//            $client = Redis::connection($connection)->client();
            /** @var \Hyperf\Redis\RedisConnection */
            $client = $this->app->get(RedisFactory::class)->get($connection)->getConnection(true);

            $this->originalPrefixes[$connection] = $client->getOption(\Redis::OPT_PREFIX);
            $client->setOption(\Redis::OPT_PREFIX, $prefix);
        }
    }

    public function revert()
    {
        foreach ($this->prefixedConnections() as $connection) {
//            $client = Redis::connection($connection)->client();
            /** @var \Hyperf\Redis\RedisConnection */
            $client = $this->app->get(RedisFactory::class)->get($connection)->getConnection(true);

            $client->setOption(\Redis::OPT_PREFIX, $this->originalPrefixes[$connection]);
        }

        $this->originalPrefixes = [];
    }

    protected function prefixedConnections()
    {
        return $this->config->get('tenancy.redis.prefixed_connections');
    }
}
