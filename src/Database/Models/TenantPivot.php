<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Models;

use Hyperf\Database\Model\Events\Saved;
//use Illuminate\Database\Eloquent\Relations\Pivot;
use Hyperf\Database\Model\Relations\Pivot;
use Stancl\Tenancy\Contracts\Syncable;

class TenantPivot extends Pivot
{
//    public static function boot()
//    {
//        parent::boot();
//
//        static::saved(function (self $pivot) {
//            $parent = $pivot->pivotParent;
//
//            if ($parent instanceof Syncable) {
//                $parent->triggerSyncEvent();
//            }
//        });
//    }

    public function saved(Saved $saved)
    {
        $parent = $this->pivotParent;

        if ($parent instanceof Syncable) {
            $parent->triggerSyncEvent();
        }
    }
}
