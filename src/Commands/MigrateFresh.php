<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Commands;

//use Illuminate\Console\Command;
use Hyperf\Command\Command;
use Stancl\Tenancy\Concerns\DealsWithMigrations;
use Stancl\Tenancy\Concerns\HasATenantsOption;

final class MigrateFresh extends Command
{
    use HasATenantsOption, DealsWithMigrations;

    /**
     * for hyperf command
     * @var string
     */
    protected $name = 'tenants:migrate-fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all tables and re-run all migrations for tenant(s)';

    public function __construct()
    {
        parent::__construct();

        $this->setName('tenants:migrate-fresh');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        tenancy()->runForMultiple($this->input->getOption('tenants'), function ($tenant) {
            $this->info('Dropping tables.');
            $this->call('db:wipe', array_filter([
                '--database' => 'tenant',
                '--force' => true,
            ]));

            $this->info('Migrating.');
            $this->call('tenants:migrate', [
                '--tenants' => [$tenant->getTenantKey()],
                '--force' => true,
            ]);
        });

        $this->info('Done.');
    }
}
