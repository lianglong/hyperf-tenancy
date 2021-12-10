<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Database\TenantScope;
use Hyperf\Database\Model\Events\Creating;

/**
 * @property-read Tenant $tenant
 */
trait BelongsToTenant
{
    public static $tenantIdColumn = 'tenant_id';

    public function tenant()
    {
        return $this->belongsTo(config('tenancy.tenant_model'), BelongsToTenant::$tenantIdColumn);
    }

    public static function bootBelongsToTenant()
    {
        static::addGlobalScope(new TenantScope);

//        static::creating(function ($model) {
//            if (! $model->getAttribute(BelongsToTenant::$tenantIdColumn) && ! $model->relationLoaded('tenant')) {
//                if (tenancy()->initialized) {
//                    $model->setAttribute(BelongsToTenant::$tenantIdColumn, tenant()->getTenantKey());
//                    $model->setRelation('tenant', tenant());
//                }
//            }
//        });
        $contextKey = 'tenancy.bootevents';
        $bootevents = app(\Stancl\Tenancy\CommonContainer::class)->getKey($contextKey,[]);
        $bootevents[get_called_class()]['creating'][] = function (Creating $creating){
            if (! $creating->getModel()->getAttribute(BelongsToTenant::$tenantIdColumn) && ! $creating->getModel()->relationLoaded('tenant')) {
                if (tenancy()->initialized) {
                    if( tenancy()->tenant instanceof \Stancl\Tenancy\Contracts\Tenant ){
                        if(
                            tenancy()->tenant instanceof \Stancl\Tenancy\Contracts\TenantWithDatabase &&
                            tenancy()->tenant->database()->getTemplateConnectionName() != config('tenancy.database.central_connection')
                        ){//租户拥有独立的连接池以及数据库，这种情况就不需要加映射字段
                            return ;
                        }
                        //租户拥有独立的数据库，这种情况也不需要加映射字段
                        if(tenancy()->tenant->getInternal('db_name')!='' && tenancy()->tenant->getInternal('db_name') != config("databases.".config('tenancy.database.central_connection').".database") ){
                            return ;
                        }
                    }
                    $creating->getModel()->setAttribute(BelongsToTenant::$tenantIdColumn, tenant()->getTenantKey());
                    $creating->getModel()->setRelation('tenant', tenant());
                }
            }
        };
        app(\Stancl\Tenancy\CommonContainer::class)->setKey($contextKey,$bootevents);
    }

}
