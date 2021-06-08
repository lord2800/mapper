<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Mapper\Generator;
use Mapper\Attributes\{Field,Subfield,Setting,Alias};

class GeneratorTest extends TestCase
{
    public function testGeneratorPicksUpFieldsCorrectly()
    {
        $class = new class {
            #[Field('text')]
            public string $test;
        };

        $generator = new Generator();
        $mapping = $generator->generate($class);

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

    public function testGeneratorPicksUpSubfieldsCorrectly()
    {
        $class = new class {
            #[Field('text')]
            public string $test;
            #[Subfield('test', 'text')]
            public string $sub;
        };

        $generator = new Generator();
        $mapping = $generator->generate($class);

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

    public function testGeneratorPicksUpSettingsCorrectly()
    {
        // phpcs:disable
        $class = new #[Setting(['this' => true])] class {};
        // phpcs:enable

        $generator = new Generator();
        $mapping = $generator->generate($class);

        self::assertJsonStringEqualsJsonString(
            json_encode([
                'settings' => [
                    'this' => true
                ]
            ]),
            json_encode($mapping)
        );
    }

    public function testGeneratorPicksUpMultipleSettingsCorrectly()
    {
        // phpcs:disable
        $class = new #[Setting(['this' => true])] #[Setting(['that' => false])] class {};
        // phpcs:enable

        $generator = new Generator();
        $mapping = $generator->generate($class);

        self::assertJsonStringEqualsJsonString(
            json_encode([
                'settings' => [
                    'this' => true,
                    'that' => false
                ]
            ]),
            json_encode($mapping)
        );
    }

    public function testGeneratorPicksUpAliasesCorrectly()
    {
        // phpcs:disable
        $class = new #[Alias('something', ['stuff' => 'here'])] class {};
        // phpcs:enable

        $generator = new Generator();
        $mapping = $generator->generate($class);

        self::assertJsonStringEqualsJsonString(
            json_encode([
                'aliases' => [
                    'something' => ['stuff' => 'here']
                ]
            ]),
            json_encode($mapping)
        );
    }
}
