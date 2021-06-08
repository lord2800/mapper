<?php

declare(strict_types=1);

namespace Mapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Field
{
    public function __construct(
        public string $type,
        public array $additionalProperties = [],
    ) {
    }
}
