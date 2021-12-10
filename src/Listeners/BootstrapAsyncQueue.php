<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Stancl\Tenancy\Listeners;

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\AsyncQueue\Event\AfterHandle;
use Hyperf\AsyncQueue\Event\BeforeHandle;
use Hyperf\AsyncQueue\Event\Event;
use Hyperf\AsyncQueue\Event\FailedHandle;
use Hyperf\AsyncQueue\Event\RetryHandle;

class BootstrapAsyncQueue implements ListenerInterface
{

    public function listen(): array
    {
        return [
            AfterHandle::class,
            BeforeHandle::class,
            FailedHandle::class,
            RetryHandle::class,
        ];
    }

    public function process(object $event)
    {
        if( $event instanceof BeforeHandle ){
            if ($event->message instanceof \Stancl\Tenancy\Queue\TenantAsyncMessage) {
                $tenantKey = $event->message->tenantKey ?? null;

                // The job is not tenant-aware
                if ( empty($tenantKey) ) {
                    return;
                }

                // Tenancy is already initialized for the tenant (e.g. dispatchNow was used)
                if (tenancy()->initialized && tenant(tenant()->getTenantKeyName()) === $tenantKey) {
                    return;
                }

                // Tenancy was either not initialized, or initialized for a different tenant.
                // Therefore, we initialize it for the correct tenant.
                tenancy()->initialize(tenancy()->find($tenantKey));
            }
        }else{

        }
    }
}