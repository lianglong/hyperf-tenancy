<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Commands;

use Hyperf\Command\Command;

class Run extends Command
{
    /**
     * for hyperf command
     * @var string
     */
    protected $name = 'tenants:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a command for tenant(s)';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "tenants:run {commandname : The command's name.}
                            {--tenants=* : The tenant(s) to run the command for. Default: current}
                            {--argument=* : The arguments to pass to the command. Default: none}
                            {--option=* : The options to pass to the command. Default: none}";

    public function configure()
    {
        parent::configure();
        $this->setHelp($this->signature);
        $this->setDescription($this->description);
    }

    protected function getArguments()
    {
        return [
            ['tenants', InputArgument::OPTIONAL, 'The tenant(s) to run the command for']
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        tenancy()->runForMultiple($this->input->getArgument('tenants'), function ($tenant) {
            $this->line("Tenant: {$tenant['id']}");
            tenancy()->initialize($tenant);

            $callback = function ($prefix = '') {
                return function ($arguments, $argument) use ($prefix) {
                    [$key, $value] = explode('=', $argument, 2);
                    $arguments[$prefix . $key] = $value;

                    return $arguments;
                };
            };

            // Turns ['foo=bar', 'abc=xyz=zzz'] into ['foo' => 'bar', 'abc' => 'xyz=zzz']
            $arguments = array_reduce($this->input->getArgument('argument'), $callback(), []);

            // Turns ['foo=bar', 'abc=xyz=zzz'] into ['--foo' => 'bar', '--abc' => 'xyz=zzz']
            $options = array_reduce($this->input->getArgument('option'), $callback('--'), []);

            // Run command
            $this->call($this->input->getArgument('commandname'), array_merge($arguments, $options));
        });
    }
}
