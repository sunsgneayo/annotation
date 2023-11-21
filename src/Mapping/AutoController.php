<?php

declare(strict_types=1);

namespace Sunsgne\Annotations\Mapping;

use Attribute;

/**
 * @Annotation
 * @Target({"ALL"})
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class AutoController extends AbstractAnnotation
{
    /**
     * @var string|array
     */
    private string|array $controller;

    public function __construct(...$value)
    {
        $this->bindMainProperty('prefix', $value);
        $this->controller = $value;
    }
    
    public function getPrefix()
    {
        return $this->controller['prefix'] ?? '';
    }
}
