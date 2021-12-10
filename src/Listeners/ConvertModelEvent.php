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
use Hyperf\Database\Model\Events\Booted;
use Hyperf\Database\Model\Events\Booting;
use Hyperf\Database\Model\Events\Created;
use Hyperf\Database\Model\Events\Creating;
use Hyperf\Database\Model\Events\Deleted;
use Hyperf\Database\Model\Events\Deleting;
use Hyperf\Database\Model\Events\ForceDeleted;
use Hyperf\Database\Model\Events\Restored;
use Hyperf\Database\Model\Events\Restoring;
use Hyperf\Database\Model\Events\Retrieved;
use Hyperf\Database\Model\Events\Saved;
use Hyperf\Database\Model\Events\Saving;
use Hyperf\Database\Model\Events\Updated;
use Hyperf\Database\Model\Events\Updating;
use Hyperf\Utils\Context;

class ConvertModelEvent implements ListenerInterface
{

    public function listen(): array
    {
        return [
//            Booting::class,
//            Booted::class,
//            Retrieved::class,
            Creating::class,
            Created::class,
            Updating::class,
            Updated::class,
            Saving::class,
            Saved::class,
//            Restoring::class,
//            Restored::class,
            Deleting::class,
            Deleted::class,
//            ForceDeleted::class,
        ];
    }

    public function process(object $event)
    {
        if( !($event->getModel() instanceof \Stancl\Tenancy\Contracts\Tenant) && !($event->getModel() instanceof \Stancl\Tenancy\Contracts\Domain) ){
            return ;
        }
        $eventNamePrefix = 'Stancl\Tenancy\Events';
        $modelName = $event->getModel() instanceof \Stancl\Tenancy\Contracts\Tenant ? 'Tenant' : 'Domain';//get_class($event->getModel());
        $methodName = ucfirst($event->getMethod());

        $dispatcher = $event->getModel()->getEventDispatcher();
        if( substr($methodName,-3) == 'ing' ){
            $eventName = "{$eventNamePrefix}\\{$methodName}{$modelName}";
        }else{
            $eventName = "{$eventNamePrefix}\\{$modelName}{$methodName}";
        }
        $dispatcher->dispatch(new $eventName($event->getModel()));
    }
}