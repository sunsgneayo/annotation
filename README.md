# webman  Annotation 


> 使用了 doctrine/annotations 包来对代码内的注解进行解析,
> 您可以直接在控制器类任意方法定义@RequestMapping注解来完成一个路由的定义
> 后续我会完善中间件的注解方法@Middwares注解来完成对针对路由的中间件定义
> 
> 感谢各位大佬批评指正

## 安装

```shell
composer require sunsgne/annotations
```
## 使用
~~~php
    use Sunsgne\Annotations\Annotations\Mapping\RequestMapping;
    /**
     * @RequestMapping(methods="GET" , path="/api/json")
     * @param Request $request
     * @return Response
     */
    public function json(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
~~~
