<?php


namespace Sunsgne\Annotations\Mapping;

use Attribute;

/**
 * @Annotation
 * Class RequestMapping
 * @package Sunsgne\Annotations\Annotations\Mapping
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class RequestMapping extends AbstractAnnotation
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

    public function __construct(...$value)
    {

        $formattedValue = $this->formatParams($value);
        $this->path    = $formattedValue["path"];
        if (isset($formattedValue['methods'])) {
            if (is_string($formattedValue['methods'])) {
                // Explode a string to a array
                $this->methods = explode(',', mb_strtoupper(str_replace(' ', '', $formattedValue['methods'])  , 'UTF-8'));
            } else {
                $methods = [];
                foreach ($formattedValue['methods'] as $method) {
                    $methods[] = mb_strtoupper(str_replace(' ', '', $method) , 'UTF-8');
                }
                $this->methods = $methods;
            }
        }
    }
}