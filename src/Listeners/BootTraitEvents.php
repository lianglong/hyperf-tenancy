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


class BootTraitEvents implements ListenerInterface
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
        if( !($event->getModel() instanceof \Hyperf\DbConnection\Model\Model)  ){
            return ;
        }
        $closures = app(\Stancl\Tenancy\CommonContainer::class)->getKey('tenancy.bootevents',[])[get_class($event->getModel())][$event->getMethod()] ??[];
        foreach ($closures as $callable){
            $callable($event);
        }
    }
}
{

}