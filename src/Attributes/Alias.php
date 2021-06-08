<?php

declare(strict_types=1);

namespace Mapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Alias
{
    public function __construct(
        public string $name,
        public array $properties = [],
    ) {
    }
}
