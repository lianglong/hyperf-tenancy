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

namespace Stancl\Tenancy\Filesystem;

use League\Flysystem\Filesystem;
use Hyperf\Filesystem\Adapter\LocalAdapterFactory;

class TenantFilesystemFactory extends \Hyperf\Filesystem\FilesystemFactory
{
    public function get($adapterName): Filesystem
    {
        $options = config('file', [
            'default' => 'local',
            'storage' => [
                'local' => [
                    'driver' => LocalAdapterFactory::class,
                    'root' => BASE_PATH . '/runtime',
                ],
            ],
        ]);
        $adapter = $this->getAdapter($options, $adapterName);
        if (\Hyperf\Filesystem\Version::isV2()) {
            return new Filesystem($adapter, $options['storage'][$adapterName] ?? [], new TenantPathNormalizer());
        }

        return new Filesystem($adapter, new Config($options['storage'][$adapterName]), new TenantPathNormalizer());
    }

}