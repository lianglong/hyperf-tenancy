<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

use Hyperf\Database\Model\Events\Saving;
use Stancl\Tenancy\Exceptions\DomainOccupiedByOtherTenantException;

trait EnsuresDomainIsNotOccupied
{
    public static function bootEnsuresDomainIsNotOccupied()
    {
//        static::saving(function ($self) {
//            if ($domain = $self->newQuery()->where('domain', $self->domain)->first()) {
//                if ($domain->getKey() !== $self->getKey()) {
//                    throw new DomainOccupiedByOtherTenantException($self->domain);
//                }
//            }
//        });
        $contextKey = 'tenancy.bootevents';
        $bootevents = app(\Stancl\Tenancy\CommonContainer::class)->getKey($contextKey,[]);
        $bootevents[get_called_class()]['saving'][] = function (Saving $saving){
            if ($domain = $saving->getModel()->newQuery()->where('domain', $saving->getModel()->domain)->first()) {
                if ($domain->getKey() !== $saving->getModel()->getKey()) {
                    throw new DomainOccupiedByOtherTenantException($saving->getModel()->domain);
                }
            }
        };
        app(\Stancl\Tenancy\CommonContainer::class)->setKey($contextKey,$bootevents);
    }

}
