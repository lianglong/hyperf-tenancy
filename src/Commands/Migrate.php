<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Commands;

//use Illuminate\Contracts\Events\Dispatcher;
//use Illuminate\Database\Console\Migrations\MigrateCommand;
use Hyperf\Command\Command as MigrateCommand;
//use Illuminate\Database\Migrations\Migrator;
use Stancl\Tenancy\Concerns\ConfirmToProceed;
use Stancl\Tenancy\Concerns\DealsWithMigrations;
use Stancl\Tenancy\Concerns\HasATenantsOption;
use Stancl\Tenancy\Events\DatabaseMigrated;
use Stancl\Tenancy\Events\MigratingDatabase;

class Migrate extends MigrateCommand
{
    use HasATenantsOption,
        DealsWithMigrations,
        ConfirmToProceed;

    /**
     * for hyperf command
     * @var string
     */
    protected $name = 'tenants:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for tenant(s)';

    /**
     * Create a new command instance.
     *
     * @param Migrator $migrator
     * @param Dispatcher $dispatcher
     */
    public function __construct()
    {
        parent::__construct();

        $this->setName('tenants:migrate');
        $this->specifyParameters();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (config('tenancy.migration_parameters') as $parameter => $value) {
            if (! $this->input->hasParameterOption($parameter)) {
                $this->input->setOption(ltrim($parameter, '-'), $value);
            }
        }

        if (! $this->confirmToProceed()) {
            return;
        }

        tenancy()->runForMultiple($this->input->getOption('tenants'), function ($tenant) {
            $this->line("Tenant: {$tenant['id']}");

            event(new MigratingDatabase($tenant));

            // Migrate
            parent::handle();

            event(new DatabaseMigrated($tenant));
        });
    }
}
