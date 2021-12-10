# hyperf-tenancy

来自于laravel的优秀library https://github.com/archtechx/tenancy ，将其移植到hyperf。

- 当前项目基于 stancl/tenancy:v3.4.5
- 项目依赖 [limingxinleo/i-cache](https://github.com/limingxinleo/i-cache) (来自laravel的illuminate/cache)。
- 由于精力有限，所有commands仅是针对继承关系简单做代码修改，尚未测试。有需求的同学欢迎完善后进行pr。
- 目前仅适配基于RequestData以及Path两种方式的解析器，Domain尚未测试(似乎hyperf集成的nikic/fast-route也没提供子域名相关功能)
##### 文档 [https://tenancyforlaravel.com/docs/v3](https://tenancyforlaravel.com/docs/v3/)

### 安装
```
    # composer require lianglong/hyperf-tenancy
    # php bin/hyperf.php tenants:install
```

### 配置
因为需要启用缓存tags，需要将 config/autoload/i_cache.php 中default默认缓存改为redis 
> 建议新建监听器针对中间件进行初始化 
```php
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
    namespace App\Listener;
    
    use Hyperf\Event\Annotation\Listener;
    use Hyperf\Event\Contract\ListenerInterface;
    use Hyperf\Framework\Event\AfterWorkerStart;
    use Hyperf\HttpMessage\Exception\ForbiddenHttpException;
    use Stancl\Tenancy\Middleware;
    use Stancl\Tenancy\Resolvers\PathTenantResolver;
    use Stancl\Tenancy\Resolvers\RequestDataTenantResolver;
    use Psr\Container\ContainerInterface;
    
    /**
     * @Listener
     */
    class TenancyInitListener implements ListenerInterface
    {
    
        /**
         * @var ContainerInterface
         */
        private $app;
    
        public function __construct(ContainerInterface $container)
        {
            $this->app = $container;
        }
    
        public function listen(): array
        {
            return [
                AfterWorkerStart::class
            ];
        }
    
        public function process(object $event)
        {
    
            /**
             * ByRequestData 租户识别方式相关设置
             */
            //租户匹配来源字段(header)
            Middleware\InitializeTenancyByRequestData::$header = 'x-tenant';
            //禁用通过查询参数匹配租户
            Middleware\InitializeTenancyByRequestData::$queryParameter = null;
            //开启租户信息缓存 #see https://tenancyforlaravel.com/docs/v3/cached-lookup
            RequestDataTenantResolver::$shouldCache = true;
            //设置租户信息缓存有效时间
            RequestDataTenantResolver::$cacheTTL = 86400;
            //租户匹配失败时提示
            Middleware\InitializeTenancyByRequestData::$onFail = function (\Exception $e, \Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Server\RequestHandlerInterface $next){
                throw new ForbiddenHttpException();
            };
            /**
             * ByPath 租户识别方式相关设置
             */
            //租户匹配来源字段 route param
            PathTenantResolver::$tenantParameterName = 'tenant';
            //开启租户信息缓存
            PathTenantResolver::$shouldCache = true;
            //设置租户信息缓存有效时间
            PathTenantResolver::$cacheTTL = 86400;
            //租户匹配失败时提示
            Middleware\InitializeTenancyByPath::$onFail = function (\Exception $e, \Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Server\RequestHandlerInterface $next){
                throw new ForbiddenHttpException();
            };
        }
    }
```
