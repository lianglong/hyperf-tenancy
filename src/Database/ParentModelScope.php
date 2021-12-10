<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database;

//use Illuminate\Database\Eloquent\Builder;
use Hyperf\Database\Model\Builder;
//use Illuminate\Database\Eloquent\Model;
use Hyperf\Database\Model\Model;
//use Illuminate\Database\Eloquent\Scope;
use Hyperf\Database\Model\Scope;

class ParentModelScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (! tenancy()->initialized) {
            return;
        }

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

        if( method_exists($builder->getModel(),'getRelationshipToPrimaryModel') ){
            $builder->whereHas($builder->getModel()->getRelationshipToPrimaryModel());
        }
    }

    public function extend(Builder $builder)
    {
        $builder->macro('withoutParentModel', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}
