<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Listeners;

use Stancl\Tenancy\Events\RevertedToCentralContext;
use Stancl\Tenancy\Events\RevertingToCentralContext;
use Stancl\Tenancy\Events\TenancyEnded;
use Hyperf\Event\Contract\ListenerInterface;

class RevertToCentralContext implements ListenerInterface
{
    public function listen(): array
    {
        return [
            TenancyEnded::class,
        ];
    }

    //hyperf的处理方法
    public function process(object $event)
    {
        $this->handle($event);
    }

    public function handle(TenancyEnded $event)
    {
        event(new RevertingToCentralContext($event->tenancy));

        foreach ($event->tenancy->getBootstrappers() as $bootstrapper) {
            $bootstrapper->revert();
        }

        event(new RevertedToCentralContext($event->tenancy));
    }
}
