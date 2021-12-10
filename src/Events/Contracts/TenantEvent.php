<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Events\Contracts;

use Stancl\Tenancy\Events\Contracts\SerializesModels;
use Stancl\Tenancy\Contracts\Tenant;

abstract class TenantEvent
{
    use SerializesModels;

    /** @var Tenant */
    public $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }
}
