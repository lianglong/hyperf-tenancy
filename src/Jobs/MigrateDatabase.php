<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Jobs;

//use Illuminate\Bus\Queueable;
//use Illuminate\Contracts\Queue\ShouldQueue;
//use Illuminate\Foundation\Bus\Dispatchable;
//use Illuminate\Queue\InteractsWithQueue;
use Stancl\Tenancy\Events\Contracts\SerializesModels;
//use Illuminate\Support\Facades\Artisan;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Hyperf\AsyncQueue\Job;

class MigrateDatabase extends Job
{
//    /** @var TenantWithDatabase */
//    protected $tenant;
//
//    public function __construct(TenantWithDatabase $tenant)
//    {
//        $this->tenant = $tenant;
//    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        Artisan::call('tenants:migrate', [
//            '--tenants' => [$this->tenant->getTenantKey()],
//        ]);
    }
}
