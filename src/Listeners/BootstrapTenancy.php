<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Listeners;

use Stancl\Tenancy\Events\BootstrappingTenancy;
use Stancl\Tenancy\Events\TenancyBootstrapped;
use Stancl\Tenancy\Events\TenancyInitialized;
use Hyperf\Event\Contract\ListenerInterface;
//use Hyperf\Framework\Event\AfterWorkerStart;

class BootstrapTenancy implements ListenerInterface
{
    public function listen(): array
    {
        return [
            TenancyInitialized::class,
        ];
    }

    //hyperf的处理方法
    public function process(object $event)
    {
        $this->handle($event);
    }

    public function handle(TenancyInitialized $event)
    {
        event(new BootstrappingTenancy($event->tenancy));

        foreach ($event->tenancy->getBootstrappers() as $bootstrapper) {
            $bootstrapper->bootstrap($event->tenancy->tenant);
        }

        event(new TenancyBootstrapped($event->tenancy));
    }


}
