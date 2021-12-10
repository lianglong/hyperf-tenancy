<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

use Hyperf\Database\Model\Events\Creating;
use Stancl\Tenancy\Contracts\UniqueIdentifierGenerator;

trait GeneratesIds
{
    public static function bootGeneratesIds()
    {
//        static::creating(function (self $model) {
//            if (! $model->getKey() && $model->shouldGenerateId()) {
//                $model->setAttribute($model->getKeyName(), app(UniqueIdentifierGenerator::class)->generate($model));
//            }
//        });
        $contextKey = 'tenancy.bootevents';
        $bootevents = app(\Stancl\Tenancy\CommonContainer::class)->getKey($contextKey,[]);
        $bootevents[get_called_class()]['creating'][] = function (Creating $creating){
            if (! $creating->getModel()->getKey() && $creating->getModel()->shouldGenerateId()) {
                $creating->getModel()->setAttribute($creating->getModel()->getKeyName(), app(UniqueIdentifierGenerator::class)->generate($creating->getModel()));
            }
        };
        app(\Stancl\Tenancy\CommonContainer::class)->setKey($contextKey,$bootevents);
    }

    public function getIncrementing()
    {
        return ! app()->has(UniqueIdentifierGenerator::class);
    }

    public function shouldGenerateId(): bool
    {
        return ! $this->getIncrementing();
    }

    public function getKeyType()
    {
        return $this->shouldGenerateId() ? 'string' : $this->keyType;
    }
}
