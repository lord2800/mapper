<?php

declare(strict_types=1);

namespace Mapper;

use ReflectionClass;
use ReflectionAttribute;
use ReflectionProperty;
use Mapper\Attributes\{Field,Setting,Alias};

final class Generator
{
    /** @psalm-param class-string|trait-string|object $class */
    public function generate(string|object $class): ?Mapping
    {
        if (is_string($class) && !class_exists($class)) {
            return null;
        }

        $obj = new ReflectionClass($class);

        $fields = [];
        $settings = [];
        $aliases = [];

        foreach ($obj->getAttributes(Setting::class, ReflectionAttribute::IS_INSTANCEOF) as $attr) {
            $settings[] = $attr->newInstance();
        }

        foreach ($obj->getAttributes(Alias::class, ReflectionAttribute::IS_INSTANCEOF) as $attr) {
            $aliases[] = $attr->newInstance();
        }

        foreach ($obj->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
            $attrs = $prop->getAttributes(Field::class, ReflectionAttribute::IS_INSTANCEOF);
            foreach ($attrs as $attr) {
                $fields[$prop->getName()][] = $attr->newInstance();
            }
        }

        return new Mapping($fields, $settings, $aliases);
    }
}
