<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Stancl\Tenancy;

use Stancl\Tenancy\Listeners\BootstrapAsyncQueue;
use Stancl\Tenancy\Listeners\BootstrapTenancy;
use Stancl\Tenancy\Contracts;
use Stancl\Tenancy\Commands;
use Stancl\Tenancy\Listeners\BootTraitEvents;
use Stancl\Tenancy\Listeners\ConvertModelEvent;
use Stancl\Tenancy\Listeners\CreateTenantConnection;
use Stancl\Tenancy\Listeners\RevertToCentralContext;
use Stancl\Tenancy\Listeners\InvalidatesResolverCache;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                Contracts\Domain::class => DomainTenantResolver::$currentDomain,
                \Hyperf\AsyncQueue\Message::class => \Stancl\Tenancy\Queue\TenantAsyncMessage::class,
                \Hyperf\Database\ConnectionResolverInterface::class => \Stancl\Tenancy\Database\ConnectionResolver::class,
            ],
            'commands' => [
                Commands\Run::class,
                Commands\Seed::class,
                Commands\Install::class,
                Commands\Migrate::class,
                Commands\Rollback::class,
                Commands\TenantList::class,
                Commands\MigrateFresh::class,
            ],
            'listeners'=>[
                BootstrapTenancy::class,
//                CreateTenantConnection::class,
                RevertToCentralContext::class,
                BootstrapAsyncQueue::class,
                ConvertModelEvent::class,
                BootTraitEvents::class
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            // 组件默认配置文件，即执行命令后会把 source 的对应的文件复制为 destination 对应的的文件
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'description of this config file.', // 描述
                    // 建议默认配置放在 publish 文件夹中，文件命名和组件名称相同
                    'source' => __DIR__ . '/../publish/config.php',  // 对应的配置文件路径
                    'destination' => BASE_PATH . '/config/autoload/tenancy.php', // 复制为这个路径下的该文件
                ],

            ],
        ];
    }
}
