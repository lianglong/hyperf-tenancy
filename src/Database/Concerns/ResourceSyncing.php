<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

use Hyperf\Database\Model\Events\Creating;
use Hyperf\Database\Model\Events\Saved;
use Stancl\Tenancy\Contracts\Syncable;
use Stancl\Tenancy\Contracts\UniqueIdentifierGenerator;
use Stancl\Tenancy\Events\SyncedResourceSaved;

trait ResourceSyncing
{
    public static function bootResourceSyncing()
    {
//        static::saved(function (Syncable $model) {
//            /** @var ResourceSyncing $model */
//            $model->triggerSyncEvent();
//        });

//        static::creating(function (self $model) {
//            if (! $model->getAttribute($model->getGlobalIdentifierKeyName()) && app()->bound(UniqueIdentifierGenerator::class)) {
//                $model->setAttribute(
//                    $model->getGlobalIdentifierKeyName(),
//                    app(UniqueIdentifierGenerator::class)->generate($model)
//                );
//            }
//        });

        $contextKey = 'tenancy.bootevents';
        $bootevents = app(\Stancl\Tenancy\CommonContainer::class)->getKey($contextKey,[]);
        $bootevents[get_called_class()]['creating'][] = function (Creating $creating){
            if (! $creating->getModel()->getAttribute($creating->getModel()->getGlobalIdentifierKeyName()) && app()->has(UniqueIdentifierGenerator::class)) {
                $creating->getModel()->setAttribute(
                    $creating->getModel()->getGlobalIdentifierKeyName(),
                    app(UniqueIdentifierGenerator::class)->generate($creating->getModel())
                );
            }
        };

        $bootevents[get_called_class()]['saved'][] = function (Saved $saved){
            $saved->getModel()->triggerSyncEvent();
        };

        app(\Stancl\Tenancy\CommonContainer::class)->setKey($contextKey,$bootevents);
    }

    public function triggerSyncEvent()
    {
        /** @var Syncable $this */
        event(new SyncedResourceSaved($this, tenant()));
    }
}
