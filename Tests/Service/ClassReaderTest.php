<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Service;

use PHPUnit\Framework\TestCase;

class ClassReaderTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_read_classes_from_directory()
    {
        $reader = new ClassReader([__DIR__ . '/../Fixture'], []);

        $expected = [
            'MultiNamespace\Bar',
            'MultiNamespace\Baz',
            'FooBar',
            'SingleNamespace\Bar',
            'SingleNamespace\Baz',
            'SingleNamespace\Foo',
            'Dummy',
        ];

        $this->assertSame($expected, $reader->read());
    }

    /**
     * @test
     */
    public function it_can_read_classes_from_directory_with_exclusion_pattern()
    {
        $reader = new ClassReader([__DIR__ . '/../Fixture'], [
            'Namespaced',
        ]);

        $expected = [
            'Dummy',
        ];

        $this->assertSame($expected, $reader->read());
    }

    /**
     * @test
     * @dataProvider getFiles
     */
    public function it_can_read_classes_from_single_file(string $filename, array $classes)
    {
        $reader = new ClassReader([], []);
        $this->assertSame($classes, $reader->readFile($filename));
    }

    public function getFiles(): array
    {
        return [
            [__DIR__ . '/../Fixture/Namespaced/SingleNamespace.php', [
                'SingleNamespace\Bar',
                'SingleNamespace\Baz',
                'SingleNamespace\Foo',
            ]],
            [__DIR__ . '/../Fixture/Namespaced/MultipleNamespace.php', [
                'MultiNamespace\Bar',
                'MultiNamespace\Baz',
                'FooBar',
            ]]
        ];
    }
}
