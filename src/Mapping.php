<?php

declare(strict_types=1);

namespace Mapper;

use JsonSerializable;
use Mapper\Attributes\{Field,Subfield,Setting,Alias};

final class Mapping implements JsonSerializable
{
    /**
     * @psalm-param array<string, array<Field|Subfield>> $properties
     * @psalm-param array<Setting> $settings
     * @psalm-param array<Alias> $aliases
     */
    public function __construct(
        private array $properties,
        private array $settings,
        private array $aliases,
    ) {
        foreach ($properties as $name => $fields) {
            foreach ($fields as $field) {
                if ($field instanceof Subfield && !array_key_exists($field->name, $properties)) {
                    throw new \Exception(
                        'Missing field \'' . $field->name . '\' for subfield \'' . $field->name . '.' . $name . '\''
                    );
                }
            }
        }
    }

    public function jsonSerialize()
    {
        $properties = $this->parseProperties($this->properties);
        $settings = $this->parseSettings($this->settings);
        $aliases = $this->parseAliases($this->aliases);

        $results = [];

        if (!empty($properties)) {
            $results['mappings'] = ['properties' => $properties];
        }

        if (!empty($settings)) {
            $results['settings'] = $settings;
        }

        if (!empty($aliases)) {
            $results['aliases'] = $aliases;
        }

        return $results;
    }

    private function parseProperties(array $properties): array
    {
        $result = [];
        foreach ($properties as $name => $fields) {
            foreach ($fields as $field) {
                $parsedField = $this->parseProperty($field);
                match (true) {
                    $field instanceof Subfield => $result[$field->name]['fields'][$name] = $parsedField,
                    $field instanceof Field => $result[$name] = $parsedField,
                    default => throw new \Exception('Field \'' . $name . '\' is not an instance of Field or Subfield'),
                };
            }
        }
        return $result;
    }

    private function parseProperty(Field $field): array
    {
        return array_merge([
            'type' => $field->type
        ], $field->additionalProperties);
    }

    private function parseSettings(array $settings): array
    {
        $result = [];
        foreach ($settings as $setting) {
            $result = array_merge($result, $setting->properties);
        }
        return $result;
    }

    private function parseAliases(array $aliases): array
    {
        $result = [];
        foreach ($aliases as $alias) {
            $result[$alias->name] = $alias->properties;
        }
        return $result;
    }
}
