<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Listeners;

use Stancl\Tenancy\Database\DatabaseManager;
use Stancl\Tenancy\Events\Contracts\TenantEvent;
use Hyperf\Event\Contract\ListenerInterface;


class CreateTenantConnection implements ListenerInterface
{
    public function listen(): array
    {
        return [
            TenantEvent::class,
        ];
    }

    //hyperf的处理方法
    public function process(object $event)
    {
        $this->handle($event);
    }

    /** @var DatabaseManager */
    protected $database;

    public function __construct(DatabaseManager $database)
    {
        $this->database = $database;
    }

    public function handle(TenantEvent $event)
    {
        $this->database->createTenantConnection($event->tenant);
    }
}
