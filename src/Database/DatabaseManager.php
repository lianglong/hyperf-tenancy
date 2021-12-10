<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database;

//use Illuminate\Config\Repository;
use Hyperf\Contract\ConfigInterface;
//use Illuminate\Contracts\Foundation\Application;
use Psr\Container\ContainerInterface;
//use Illuminate\Database\DatabaseManager as BaseDatabaseManager;
//use Hyperf\DbConnection\ConnectionResolver;
use Stancl\Tenancy\Contracts\TenantCannotBeCreatedException;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Exceptions\DatabaseManagerNotRegisteredException;
use Stancl\Tenancy\Exceptions\TenantDatabaseAlreadyExistsException;

/**
 * @internal Class is subject to breaking changes in minor and patch versions.
 */
class DatabaseManager
{
    /** @var ContainerInterface */
    protected $app;

    /** @var ConfigInterface */
    protected $config;

    public function __construct(ContainerInterface $app, ConfigInterface $config)
    {
        $this->app = $app;
//        $this->database = $database;
        $this->config = $config;
    }

    /**
     * Connect to a tenant's database.
     */
    public function connectToTenant(TenantWithDatabase $tenant)
    {
//        $this->database->purge('tenant');
        $this->createTenantConnection($tenant);
//        $this->setDefaultConnection($this->getTenantDatabasesConfigKey($tenant));
    }

    /**
     * Reconnect to the default non-tenant connection.
     */
    public function reconnectToCentral()
    {
//        if (tenancy()->initialized) {
//            $this->database->purge('tenant');
//        }
        $this->setDefaultConnection($this->config->get('tenancy.database.central_connection','default'));
    }

    /**
     * Change the default database connection config.
     */
    public function setDefaultConnection(string $connection)
    {
//        $this->app['config']['database.default'] = $connection;
    }

    /**
     * Create the tenant database connection.
     */
    public function createTenantConnection(TenantWithDatabase $tenant)
    {
//        $this->app['config']['database.connections.tenant'] = $tenant->database()->connection();
        $this->config->set('databases.'.$this->getTenantDatabasesConfigKey($tenant),$tenant->database()->connection());
    }

    /**
     * Check if a tenant can be created.
     *
     * @throws TenantCannotBeCreatedException
     * @throws DatabaseManagerNotRegisteredException
     * @throws TenantDatabaseAlreadyExistsException
     */
    public function ensureTenantCanBeCreated(TenantWithDatabase $tenant): void
    {
        if ($tenant->database()->manager()->databaseExists($database = $tenant->database()->getName())) {
            throw new TenantDatabaseAlreadyExistsException($database);
        }
    }

    /**
     * 动态写入租户的数据库配置数组键值
     * @param TenantWithDatabase $tenant
     * @return string
     */
    private function getTenantDatabasesConfigKey(TenantWithDatabase $tenant)
    {
        /**
         * 租户没有单独配置数据库连接参数时，使用默认的链接。
         */
        $defaultConnection = $this->config->get('tenancy.database.central_connection','default') ;
        if( $tenant->database()->getTemplateConnectionName() != $defaultConnection ){
            return 'tenant'.$tenant->getTenantKey();
        }
        return $defaultConnection;
    }
}
