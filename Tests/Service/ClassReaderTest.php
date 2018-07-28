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
     * @dataProvider getFiles
     */
    public function it_can_read_classes_from_file(string $filename, array $classes)
    {
        $reader = new ClassReader();
        $this->assertSame($classes, $reader->read($filename));
    }

    public function getFiles(): array
    {
        return [
            [__DIR__.'/../Fixture/SingleNamespace.php', [
                'SingleNamespace\Bar',
                'SingleNamespace\Baz',
                'SingleNamespace\Foo',
            ]],
            [__DIR__.'/../Fixture/MultipleNamespace.php', [
                'MultiNamespace\Bar',
                'MultiNamespace\Baz',
                'FooBar',
            ]]
        ];
    }
}
