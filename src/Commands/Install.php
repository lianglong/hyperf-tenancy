<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Commands;

//use Illuminate\Console\Command;
use Hyperf\Command\Command;

class Install extends Command
{
    /**
     * for hyperf command
     * @var string
     */
    protected $name = 'tenants:install';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install lianglong/hyperf-tenancy.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->comment('Installing lianglong/hyperf-tenancy...');
        $this->call('vendor:publish', [
            '--package' => 'lianglong/hyperf-tenancy',
        ]);
        $this->info('✔️  Created config/tenancy.php');

        if( !file_exists(BASE_PATH.'/config/i_cache.php') ){
            $this->call('vendor:publish', [
                '--package' => 'limingxinleo/i-cache',
            ]);
            $this->info('✔️  Created config/i_cache.php');
        }

//        if (! file_exists(base_path('routes/tenant.php'))) {
//            $this->callSilent('vendor:publish', [
//                '--provider' => 'Stancl\Tenancy\TenancyServiceProvider',
//                '--tag' => 'routes',
//            ]);
//            $this->info('✔️  Created routes/tenant.php');
//        } else {
//            $this->info('Found routes/tenant.php.');
//        }

//        $this->call('vendor:publish', [
//            '--provider' => 'Stancl\Tenancy\TenancyServiceProvider',
//            '--tag' => 'providers',
//        ]);
//        $this->info('✔️  Created TenancyServiceProvider.php');
//
//        $this->call('vendor:publish', [
//            '--provider' => 'Stancl\Tenancy\TenancyServiceProvider',
//            '--tag' => 'migrations',
//        ]);
//        $this->info('✔️  Created migrations. Remember to run [php artisan migrate]!');
//
//        if (! is_dir(database_path('migrations/tenant'))) {
//            mkdir(database_path('migrations/tenant'));
//            $this->info('✔️  Created database/migrations/tenant folder.');
//        }

        $this->comment('✨️ lianglong/hyperf-tenancy installed successfully.');
    }
}
