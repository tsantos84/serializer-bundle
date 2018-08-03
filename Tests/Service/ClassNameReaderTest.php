<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use TSantos\SerializerBundle\Service\ClassNameReader;

class ClassNameReaderTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_read_classes_from_directory()
    {
        $reader = new ClassNameReader();

        $expected = [
            'MultiNamespace\Bar',
            'MultiNamespace\Baz',
            'FooBar',
            'SingleNamespace\Bar',
            'SingleNamespace\Baz',
            'SingleNamespace\Foo',
            'Dummy',
        ];

        $actual = $reader->readDirectory([__DIR__.'/../Fixture']);

        sort($expected);
        sort($actual);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function it_can_read_classes_from_directory_with_exclusion_pattern()
    {
        $reader = new ClassNameReader();

        $expected = [
            'Dummy',
        ];

        $this->assertSame($expected, $reader->readDirectory([__DIR__.'/../Fixture'], ['Namespaced']));
    }

    /**
     * @test
     * @dataProvider getFiles
     */
    public function it_can_read_classes_from_single_file(string $filename, array $classes)
    {
        $reader = new ClassNameReader();
        $this->assertSame($classes, $reader->readFile($filename));
    }

    public function getFiles(): array
    {
        return [
            [__DIR__.'/../Fixture/Namespaced/SingleNamespace.php', [
                'SingleNamespace\Bar',
                'SingleNamespace\Baz',
                'SingleNamespace\Foo',
            ]],
            [__DIR__.'/../Fixture/Namespaced/MultipleNamespace.php', [
                'MultiNamespace\Bar',
                'MultiNamespace\Baz',
                'FooBar',
            ]],
        ];
    }
}
