<?php

declare(strict_types=1);

namespace Mapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Subfield extends Field
{
    public function __construct(
        public string $name,
        string $type,
        array $additionalProperties = [],
    ) {
        parent::__construct($type, $additionalProperties);
    }
}
