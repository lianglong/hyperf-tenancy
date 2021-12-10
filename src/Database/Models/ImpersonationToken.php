<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Models;

use Carbon\Carbon;
use Hyperf\Database\Model\Events\Creating;
//use Illuminate\Database\Eloquent\Model;
use Hyperf\DbConnection\Model\Model;
//use Illuminate\Support\Str;
use Hyperf\Utils\Str;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @param string $token
 * @param string $tenant_id
 * @param string $user_id
 * @param string $auth_guard
 * @param string $redirect_url
 * @param Carbon $created_at
 */
class ImpersonationToken extends Model
{
    use CentralConnection;

    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'token';
    public $incrementing = false;
    protected $table = 'tenant_user_impersonation_tokens';
    protected $dates = [
        'created_at',
    ];

//    public static function boot()
//    {
//        parent::boot();
//
//        static::creating(function ($model) {
//            $model->created_at = $model->created_at ?? $model->freshTimestamp();
//            $model->token = $model->token ?? Str::random(128);
//            $model->auth_guard = $model->auth_guard ?? config('auth.defaults.guard');
//        });
//    }

    public function creating(Creating $creating)
    {
        $creating->getModel()->created_at = $creating->getModel()->created_at ?? $creating->getModel()->freshTimestamp();
        $creating->getModel()->token = $creating->getModel()->token ?? Str::random(128);
        $creating->getModel()->auth_guard = $creating->getModel()->auth_guard ?? config('auth.defaults.guard');
    }
}
