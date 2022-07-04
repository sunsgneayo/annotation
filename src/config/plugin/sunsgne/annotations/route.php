<?php

use Doctrine\Common\Annotations\AnnotationReader;
use Sunsgne\Annotations\Mapping\Middleware;
use Sunsgne\Annotations\Mapping\Middlewares;
use Webman\Route;
use Sunsgne\Annotations\Mapping\RequestMapping;

/** @var  $routes *已经设置过路由的uri则忽略 */
$routes = Route::getRoutes();
$ignore_list = [];
foreach ($routes as $tmp_route) {
    $ignore_list[$tmp_route->getPath()] = 0;
}

/** @var  $suffix *读取config*/
$suffix = config('app.controller_suffix', '');
$suffix_length = strlen($suffix);

/** @var  $dir_iterator *递归遍历目录查找控制器自动设置路由 */
$dir_iterator = new \RecursiveDirectoryIterator(app_path());
$iterator = new \RecursiveIteratorIterator($dir_iterator);

foreach ($iterator as $file) {
    /** 忽略目录和非php文件 */
    if (is_dir($file) || $file->getExtension() != 'php') {
        continue;
    }

    $file_path = str_replace('\\', '/', $file->getPathname());
    /** 文件路径里不带controller的文件忽略 */
    if (!str_contains(strtolower($file_path), '/controller/')) {
        continue;
    }

    /**  只处理带 controller_suffix 后缀的 */
    if ($suffix_length && substr($file->getBaseName('.php'), -$suffix_length) !== $suffix) {
        continue;
    }

    // 根据文件路径是被类名
    /** @var  $class_name *根据文件路径获取类名 */
    $class_name = str_replace('/', '\\', substr(substr($file_path, strlen(base_path())), 0, -4));

    if (!class_exists($class_name)) {
        echo "Class $class_name not found, skip route for it\n";
        continue;
    }
    if (floatval(PHP_VERSION) > 8)
    {
        $controller = new ReflectionClass($class_name);
        foreach ($controller->getMethods(ReflectionMethod::IS_PUBLIC) as $k => $reflectionMethod) {
            $middlewares = '';
            $path        = "";
            $methods     = "";
            foreach ($reflectionMethod->getAttributes() as $kk => $attribute) {
                if ($attribute->getName() === Middleware::class)
                {
                    $middlewares = $attribute->getArguments();
                }
                if ($attribute->getName() === Middlewares::class)
                {
                    $middlewares = $attribute->getArguments();
                }
                if ($attribute->getName() === RequestMapping::class)
                {
                    $path = $attribute->getArguments()["path"]?? "";
                    $methods = $attribute->newInstance()->setMethods();
                }
            }

            if (!empty($methods) and !empty($path))
            {
                if (!empty($middlewares))
                {
                    Route::add($methods, $path, [$class_name, $reflectionMethod->name])->middleware($middlewares);
                }else{
                    Route::add($methods, $path, [$class_name, $reflectionMethod->name]);
                }
            }
        }

    }


    /** php8.0以下版本==通过反射找到这个类的所有共有方法作为action */
    $class = new ReflectionClass($class_name);
    foreach (config("plugin.sunsgne.annotations.ignored") as $v) {
        AnnotationReader::addGlobalIgnoredName($v);
    }
    $class_name = $class->name;
    $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
    $reader = new AnnotationReader();
    /** @var  $class_annos *注解的读取类 */
    $class_annos = $reader->getClassAnnotations($class);
    /** @var  $item *设置路由 */
    foreach ($methods as $item) {
        /** @var  $action */
        $action = $item->name;
        if (in_array($action, ['__construct', '__destruct'])) {
            continue;
        }
        /** @var  $methodAnnotation *获取@requestmapping的参数 */
        $methodAnnotation = $reader->getMethodAnnotation($item, RequestMapping::class);
        /** @var  $middlewareAnnotation *单个中间件注解参数*/
        $middlewareAnnotation  = $reader->getMethodAnnotation($item, Middleware::class);
        /** @var  $middlewareAnnotation *多个个中间件注解参数*/
        $middlewaresAnnotation = $reader->getMethodAnnotation($item, Middlewares::class);
        if (empty($methodAnnotation)) {
            continue;
        }
        $middlewares = [];
        if (!empty($middlewareAnnotation)) {
            foreach ($middlewareAnnotation as  $obj)
            {
                $middlewares = $obj[0]['value'] ?? [];
            }
        }
        if (!empty($middlewaresAnnotation)) {
            foreach ($middlewaresAnnotation->middlewares as  $objs)
            {
                $middlewares[] = $objs->middleware[0]['value'] ?? "";
            }
        }
        Route::add($methodAnnotation->methods, $methodAnnotation->path, [$class_name, $action])->middleware($middlewares);

    }
}



