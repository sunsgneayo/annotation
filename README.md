# webman  annotation 注解


> 使用了 doctrine/annotations 包来对代码内的注解进行解析。
> 
> 您可以直接在控制器类任意方法定义@RequestMapping注解来完成一个路由的定义，如需使用路由中间件请定义该路由的注解方法@Middwares或@Middware
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
> 使用 @Middleware 注解时需 use Hyperf\HttpServer\Annotation\Middleware; 命名空间；

> 使用 @Middlewares 注解时需 use Hyperf\HttpServer\Annotation\Middlewares; 命名空间；
 - @Middleware 注解为定义单个中间件时使用，在一个地方仅可定义一个该注解，不可重复定义
 - @Middlewares 注解为定义多个中间件时使用，在一个地方仅可定义一个该注解，然后通过在该注解内定义多个 @Middleware 注解实现多个中间件的定义
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