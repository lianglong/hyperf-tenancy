<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Bootstrappers;

use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Database\DatabaseManager;
use Stancl\Tenancy\Exceptions\TenantDatabaseDoesNotExistException;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class DatabaseTenancyBootstrapper implements TenancyBootstrapper
{
    /** @var DatabaseManager */
    protected $database;

    public function __construct(DatabaseManager $database)
    {
        $this->database = $database;
    }

    public function bootstrap(Tenant $tenant)
    {
        /** @var TenantWithDatabase $tenant */

        // Better debugging, but breaks cached lookup in prod
//        if (config('app_env')=='dev') {
//            $database = $tenant->database()->getName();
//            if (! $tenant->database()->manager()->databaseExists($database)) {
//                throw new TenantDatabaseDoesNotExistException($database);
//            }
//        }

        $this->database->connectToTenant($tenant);
    }

    public function revert()
    {
        $this->database->reconnectToCentral();
    }
}