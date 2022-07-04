# webman  annotation 注解


> 使用了 `doctrine/annotations` 包来对代码内的注解进行解析。支持`php8注解方式`
>
> 您可以直接在控制器类任意方法定义`@RequestMapping`注解来完成一个路由的定义，如需使用路由中间件请定义该路由的注解方法`@Middwares`或`@Middware`
> 注解并引入中间件命名空间即可
>
> 感谢各位大佬批评指正，不足之处，还望海涵！

## 安装

```shell
composer require sunsgne/annotations
```
## 使用
### 路由控制
- GET
- POST
- PUT
- DELETE
- HEADER
- OPTIONS
~~~php
use Sunsgne\Annotations\Mapping\RequestMapping;
/**
 * 允许通过 GET 或 POST 方式请求
 * @RequestMapping(methods="GET , POST" , path="/api/json")
 * @param Request $request
 * @return Response
 */
public function json(Request $request)
{
    return json(['code' => 0, 'msg' => 'ok']);
}
~~~
### 路由中间件
在通过注解定义路由时，您仅可通过注解的方式来定义中间件，对中间件的定义有两个注解，分别为：
> 使用 `@Middleware` 注解时需 `use  Sunsgne\Annotations\Mapping\Middleware;` 命名空间；

> 使用 `@Middlewares` 注解时需 `use  Sunsgne\Annotations\Mapping\Middlewares;` 命名空间；
- `@Middleware` 注解为定义单个中间件时使用，在一个地方仅可定义一个该注解，不可重复定义
- `@Middlewares` 注解为定义多个中间件时使用，在一个地方仅可定义一个该注解，然后通过在该注解内定义多个 `@Middleware` 注解实现多个中间件的定义
  定义单个中间件：
~~~php
use Sunsgne\Annotations\Mapping\RequestMapping;
use Sunsgne\Annotations\Mapping\Middleware;
use Sunsgne\Annotations\Mapping\Middlewares;
use app\middleware\App;
use app\middleware\Log;
/**
 * @RequestMapping(methods="GET" , path="/api/json")
 * @Middleware(App::class)
 * @param Request $request
 * @return Response
 */
public function json(Request $request)
{
    return json(['code' => 0, 'msg' => 'ok']);
}
~~~

定义多个中间件：
~~~php
use Sunsgne\Annotations\Mapping\RequestMapping;
use Sunsgne\Annotations\Mapping\Middleware;
use Sunsgne\Annotations\Mapping\Middlewares;
use app\middleware\App;
use app\middleware\Log;
/**
 * @RequestMapping(methods="GET" , path="/api/json")
 * @Middlewares({
 *     @Middleware(App::class),
 *     @Middleware(Log::class)
 * })
 * @param Request $request
 * @return Response
 */
public function json(Request $request)
{
    return json(['code' => 0, 'msg' => 'ok']);
}
~~~
### 支持PHP8.0+版本
** **
*注意请勿直接copy。示例中未创建中间件
1. 定义路由
~~~php
use Sunsgne\Annotations\Mapping\RequestMapping;
use Sunsgne\Annotations\Mapping\Middleware;
use Sunsgne\Annotations\Mapping\Middlewares;
use app\middleware\App;
use app\middleware\Log;
#[RequestMapping(methods: "GET , POST" , path:"/api/json")]
public function json(Request $request)
{
    return json(['code' => 0, 'msg' => 'ok']);
}
~~~

2. 定义路由并配置中间件(多个)
~~~php
use Sunsgne\Annotations\Mapping\RequestMapping;
use Sunsgne\Annotations\Mapping\Middleware;
use Sunsgne\Annotations\Mapping\Middlewares;
use app\middleware\App;
use app\middleware\Log;
#[RequestMapping(methods: "GET , POST" , path:"/api/json") , Middlewares(App::class , Log::class)]
public function json(Request $request)
{
    return json(['code' => 0, 'msg' => 'ok']);
}
~~~
### 忽略注解参数
请在`config/plugin/sunsgne/annotations/ignored`文件中添加需要忽略的参数
~~~php
return [
    "after", "afterClass", "backupGlobals", "backupStaticAttributes", "before", "beforeClass", "codeCoverageIgnore*",
    "covers", "coversDefaultClass", "coversNothing", "dataProvider", "depends", "doesNotPerformAssertions",
    "expectedException", "expectedExceptionCode", "expectedExceptionMessage", "expectedExceptionMessageRegExp", "group",
    "large", "medium", "preserveGlobalState", "requires", "runTestsInSeparateProcesses", "runInSeparateProcess", "small",
    "test", "testdox", "testWith", "ticket", "uses" , "datetime" 
    // ........
];
~~~

### 更新日志
#### 1.1.2 - 2022-07-04
- 修复注解含有`混杂参数`，导致读取失败的问题
- 新增配置文件`ignored.php`，用于对注解中的其他参数做忽略读取操作：如`datetime`,`used`等。
- 对`php8`以上版本做`原生注解`的适配