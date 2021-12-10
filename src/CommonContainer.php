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

namespace Stancl\Tenancy;


class CommonContainer
{
    private $container = [];

    public function getKey(string $keyName,$default=null)
    {
        return $this->container[$keyName] ?? $default;
    }


    public function setKey(string $keyName, array $contents)
    {
        $this->container[$keyName] = $contents;
    }
}