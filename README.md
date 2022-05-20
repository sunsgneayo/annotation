# webman  Annotation 


> 使用了 doctrine/annotations 包来对代码内的注解进行解析，注解必须写在下面示例的标准注释块才能被正确解析，其它格式均不能被正确解析。 注释块示例：


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