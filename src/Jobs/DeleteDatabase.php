<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Jobs;

//use Illuminate\Bus\Queueable;
//use Illuminate\Contracts\Queue\ShouldQueue;
//use Illuminate\Foundation\Bus\Dispatchable;
//use Illuminate\Queue\InteractsWithQueue;
//use Stancl\Tenancy\Events\Contracts\SerializesModels;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Events\DatabaseDeleted;
use Stancl\Tenancy\Events\DeletingDatabase;
use Hyperf\AsyncQueue\Job;

class DeleteDatabase  extends Job
{

//    /** @var TenantWithDatabase */
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

        event(new DeletingDatabase($tenant));

        $tenant->database()->manager()->deleteDatabase($tenant);

        event(new DatabaseDeleted($tenant));
    }
}
