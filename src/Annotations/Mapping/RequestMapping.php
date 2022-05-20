<?php


namespace Sunsgne\Annotations\Annotations\Mapping;

/**
 * @Annotation
 * Class RequestMapping
 * @package Sunsgne\Annotations\Annotations\Mapping
 */
class RequestMapping
{
    public const GET = 'GET';

    public const POST = 'POST';

    public const PUT = 'PUT';

    public const PATCH = 'PATCH';

    public const DELETE = 'DELETE';

    public const HEADER = 'HEADER';

    public const OPTIONS = 'OPTIONS';

    public $methods;

    public $path;

    public function __construct(array $options)
    {

        $this->methods = $options["methods"];
        $this->path    = $options["path"];
    }
}