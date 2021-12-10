<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Jobs;

//use Illuminate\Bus\Queueable;
//use Illuminate\Contracts\Queue\ShouldQueue;
use Hyperf\DbConnection\Model\Model;
//use Illuminate\Foundation\Bus\Dispatchable;
//use Illuminate\Queue\InteractsWithQueue;
use Stancl\Tenancy\Events\Contracts\SerializesModels;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
//use Stancl\Tenancy\Database\DatabaseManager;
use Stancl\Tenancy\Events\CreatingDatabase;
use Stancl\Tenancy\Events\DatabaseCreated;
use Hyperf\AsyncQueue\Job;

class CreateDatabase extends Job
{

//    /** @var TenantWithDatabase|Model */
//    protected $tenant;
//
//    public function __construct(TenantWithDatabase $tenant)
//    {
//        $this->tenant = $tenant;
//    }

    public function handle()
    {
        /** @var TenantWithDatabase|Model $tenant */
        $tenant = tenancy()->tenant;
        event(new CreatingDatabase($tenant));

        // Terminate execution of this job & other jobs in the pipeline
        if ($tenant->getInternal('create_database') === false) {
            return false;
        }

//        DatabaseManager $databaseManager
//        $databaseManager->ensureTenantCanBeCreated($tenant);
        if ($tenant->database()->manager()->databaseExists($database = $tenant->database()->getName())) {
            throw new \Stancl\Tenancy\Exceptions\TenantDatabaseAlreadyExistsException($database);
        }
        $tenant->database()->makeCredentials();
        $tenant->database()->manager()->createDatabase($tenant);

        event(new DatabaseCreated($tenant));
    }
}
