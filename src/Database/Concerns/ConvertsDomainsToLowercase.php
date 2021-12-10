<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

use Hyperf\Database\Model\Events\Saving;
use Hyperf\Utils\Context;

trait ConvertsDomainsToLowercase
{
    public static function bootConvertsDomainsToLowercase()
    {
//        static::saving(function ($model) {
//            $model->domain = strtolower($model->domain);
//        });
        $contextKey = 'tenancy.bootevents';
        $bootevents = app(\Stancl\Tenancy\CommonContainer::class)->getKey($contextKey,[]);
        $bootevents[get_called_class()]['saving'][] = function (Saving $event){
            $event->getModel()->domain = strtolower($event->getModel()->domain);
        };
        app(\Stancl\Tenancy\CommonContainer::class)->setKey($contextKey,$bootevents);
    }

}
