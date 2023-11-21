<?php
declare(strict_types=1);

namespace Sunsgne\Annotations;

use ReflectionClass;
use ReflectionMethod;
use SplFileInfo;
use Sunsgne\Annotations\Mapping\AutoController;
use Sunsgne\Annotations\Mapping\Middleware;
use Sunsgne\Annotations\Mapping\Middlewares;
use Webman\Route;
use Sunsgne\Annotations\Mapping\RequestMapping;

/**
 * @Time 2023/11/21 16:10
 * @author sunsgne
 */
class AnnotationRouter
{
    public static function run(): void
    {
        // 检查PHP版本
        if (floatval(PHP_VERSION) < 8) {
            throw new \RuntimeException('PHP version must be greater than 8.0. Your current version is ' . PHP_VERSION);
        }

        // 获取已设置路由的URI列表
        $routes = Route::getRoutes();
        $ignoreList = array_flip(array_map(fn($tmpRoute) => $tmpRoute->getPath(), $routes));

        // 读取配置
        $controllerSuffix = config('app.controller_suffix', '');
        $suffixLength = strlen($controllerSuffix);

        // 遍历控制器目录
        $dirIterator = new \RecursiveDirectoryIterator(app_path());
        $iterator = new \RecursiveIteratorIterator($dirIterator);

        foreach ($iterator as $file) {
            self::readFile($file, $suffixLength, $controllerSuffix, $ignoreList);
        }
    }

    protected static function readFile(SplFileInfo $file, int $suffixLength, string $controllerSuffix, array $ignoreList): void
    {
        // 忽略非PHP文件和目录
        if ($file->getExtension() != 'php' || !str_contains(strtolower($file->getPathname()), '/controller/')) {
            return;
        }

        // 处理带 controller_suffix 后缀的文件
        if ($suffixLength && substr($file->getBaseName('.php'), -$suffixLength) !== $controllerSuffix) {
            return;
        }

        // 根据文件路径获取类名
        $className = str_replace('/', '\\', substr(substr($file->getPathname(), strlen(base_path())), 0, -4));

        if (!class_exists($className)) {
            throw new \RuntimeException("Class $className not found, skip route for it");
        }

        self::addRouter($className, $ignoreList);
    }

    protected static function addRouter(string $className, array $ignoreList): void
    {
        $controller = new ReflectionClass($className);
        $attributes = $controller->getAttributes(AutoController::class);
        $prefixValue = '';

        if (!empty($attributes)) {
            $autoControllerAttribute = $attributes[0];
            $autoControllerInstance = $autoControllerAttribute->newInstance();
            $prefixValue = $autoControllerInstance->getPrefix();
        }

        foreach ($controller->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            [$middlewares, $path, $methods] = self::getMethodAttributes($reflectionMethod->getAttributes());

            if (!empty($methods) && !empty($path)) {
                $path = self::addLeadingSlashIfNeeded($prefixValue) . self::addLeadingSlashIfNeeded($path);

                if (!isset($ignoreList[$path])) {
                    $middlewareHandler = (!empty($middlewares))
                        ? fn() => Route::add($methods, $path, [$className, $reflectionMethod->name])->middleware($middlewares)
                        : fn() => Route::add($methods, $path, [$className, $reflectionMethod->name]);

                    $middlewareHandler();
                }
            }
        }
    }

    private static function getMethodAttributes(array $attributes): array
    {
        $middlewares = '';
        $path = '';
        $methods = '';

        foreach ($attributes as $attribute) {
            if ($attribute->getName() === Middleware::class || $attribute->getName() === Middlewares::class) {
                $middlewares = $attribute->getArguments();
            }

            if ($attribute->getName() === RequestMapping::class) {
                $path = $attribute->getArguments()['path'] ?? '';
                $methods = $attribute->newInstance()->setMethods();
            }
        }

        return [$middlewares, $path, $methods];
    }

    private static function addLeadingSlashIfNeeded(string $inputString): string
    {
        return (!str_starts_with($inputString, '/')) ? '/' . $inputString : $inputString;
    }
}
