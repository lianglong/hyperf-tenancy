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

namespace Stancl\Tenancy\Database;


class ConnectionResolver extends \Hyperf\DbConnection\ConnectionResolver
{
    /**
     * Get a database connection instance.
     *
     * @param string $name
     * @return \Hyperf\Database\ConnectionInterface
     */
    public function connection($name = null)
    {
        if( tenancy()->initialized ){
            if( tenancy()->tenant instanceof \Stancl\Tenancy\Contracts\TenantWithDatabase ){
                $name = tenancy()->tenant->database()->getTemplateConnectionName();
            }else{
                $name = config('tenancy.database.central_connection');
            }
        }
        return parent::connection($name);
    }
}