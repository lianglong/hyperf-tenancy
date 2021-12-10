# hyperf-tenancy

来自于laravel的优秀library https://github.com/archtechx/tenancy ，将其移植到hyperf。

- 当前项目基于 stancl/tenancy:v3.4.5
- 项目依赖 [limingxinleo/i-cache](https://github.com/limingxinleo/i-cache) (来自laravel的TaggedCache)。
- 由于精力有限，所有commands仅是针对继承关系简单做代码修改，尚未测试。有需求的同学欢迎完善后进行pr。
- 目前仅适配基于RequestData以及Path两种方式的解析器，Domain尚未测试(似乎hyperf集成的nikic/fast-route也没提供子域名相关功能)
### [Documentation(tenancyforlaravel)](https://tenancyforlaravel.com/docs/v3/)