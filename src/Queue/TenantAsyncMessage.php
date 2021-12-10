<?php
/**
 * Created by PhpStorm.
 * User: loong
 * Date: 12/4/21
 * Time: 3:01 PM
 */

namespace Stancl\Tenancy\Queue;

use Hyperf\AsyncQueue\JobInterface;
use Hyperf\AsyncQueue\Message;
use Hyperf\Contract\UnCompressInterface;

class TenantAsyncMessage extends Message
{
    /**
     * @var string
     */
    public $tenantKey;

    public function __construct(JobInterface $job)
    {
        parent::__construct($job);
//        property_exists()
        if ( empty($this->tenantKey) && tenancy()->initialized) {
            $tenantKey = tenant()->getTenantKeyName();
            $this->tenantKey = tenant($tenantKey);
        }
    }

    public function serialize()
    {
        return serialize([
            $this->job,
            $this->attempts,
            $this->tenantKey,
        ]);
    }

    public function unserialize($serialized)
    {
        [$job, $attempts, $tenantKey] = unserialize($serialized);
        if ($job instanceof UnCompressInterface) {
            $job = $job->uncompress();
        }
        $this->job = $job;
        $this->attempts = $attempts;
        $this->tenantKey = $tenantKey;
    }

}