<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Models;

use Carbon\Carbon;
//use Illuminate\Database\Eloquent\Model;
use Hyperf\DbConnection\Model\Model;
use Stancl\Tenancy\Contracts;
use Stancl\Tenancy\Database\Concerns;
use Stancl\Tenancy\Database\TenantCollection;
use Stancl\Tenancy\Events;

/**
 * @property string|int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property array $data
 *
 * @method static TenantCollection all($columns = ['*'])
 */
class Tenant extends Model implements Contracts\TenantWithDatabase
{
    use Concerns\CentralConnection,
        Concerns\GeneratesIds,
        Concerns\HasDataColumn,
        Concerns\HasInternalKeys,
        Concerns\TenantRun,
        Concerns\HasDatabase,
        Concerns\InvalidatesResolverCache;

    protected $table = 'tenants';
    protected $primaryKey = 'id';
    protected $guarded = [];


    public function getTenantKeyName(): string
    {
        return 'name';
    }

    public function getTenantKey()
    {
        return $this->getAttribute($this->primaryKey);
    }

    public function newCollection(array $models = []): TenantCollection
    {
        return new TenantCollection($models);
    }

}
