<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Mapper\Mapping;
use Mapper\Attributes\{Field,Subfield,Setting,Alias};
use Exception;

class MappingTest extends TestCase
{
    public function testMappingProducesValidFields()
    {
        $mapping = new Mapping([
            'test' => [new Field('text')]
        ], [], []);

        self::assertJsonStringEqualsJsonString(
            json_encode([
                'mappings' => [
                    'properties' => [
                        'test' => [
                            'type' => 'text'
                        ]
                    ]
                ]
            ]),
            json_encode($mapping)
        );
    }

    public function testMappingProducesValidSubfields()
    {
        $mapping = new Mapping([
            'test' => [new Field('text')],
            'sub' => [new Subfield('test', 'text')]
        ], [], []);

        self::assertJsonStringEqualsJsonString(
            json_encode([
                'mappings' => [
                    'properties' => [
                        'test' => [
                            'type' => 'text',
                            'fields' => [
                                'sub' => [
                                    'type' => 'text'
                                ]
                            ]
                        ]
                    ]
                ]
            ]),
            json_encode($mapping)
        );
    }

    public function testMappingThrowsWithInvalidSubfield()
    {
        self::expectException(Exception::class, 'Missing field test for subfield test.sub');

        $mapping = new Mapping([
            'sub' => [new Subfield('test', 'text')]
        ], [], []);
    }

    public function testMappingProducesValidSettings()
    {
        $mapping = new Mapping([], [
            new Setting(['this' => true])
        ], []);

        self::assertJsonStringEqualsJsonString(
            json_encode([
                'settings' => [
                    'this' => true
                ]
            ]),
            json_encode($mapping)
        );
    }

    public function testMappingProducesValidSettingsWithMultipleSettings()
    {
        $mapping = new Mapping([], [
            new Setting(['this' => true]),
            new Setting(['that' => false])
        ], []);

        self::assertJsonStringEqualsJsonString(
            json_encode([
                'settings' => [
                    'this' => true,
                    'that' => false,
                ]
            ]),
            json_encode($mapping)
        );
    }

    public function testMappingProducesValidAliases()
    {
        $mapping = new Mapping([], [], [
            new Alias('test', ['stuff' => 'here'])
        ]);

        self::assertJsonStringEqualsJsonString(
            json_encode([
                'aliases' => [
                    'test' => ['stuff' => 'here']
                ]
            ]),
            json_encode($mapping)
        );
    }
}
