<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests\Serializer;

use Metadata\MetadataFactoryInterface;
use PHPUnit\Framework\TestCase;
use TSantos\Serializer\HydratorCodeGenerator;
use TSantos\Serializer\HydratorCodeWriter;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\SerializerBundle\Serializer\Compiler;

class CompilerTest extends TestCase
{
    /** @test */
    public function it_should_compile_a_class()
    {
        $metadataFactory = $this->createMock(MetadataFactoryInterface::class);
        $metadataFactory
            ->expects($this->once())
            ->method('getMetadataForClass')
            ->with('My\\Class')
            ->willReturn($metadata = $this->createMock(ClassMetadata::class));

        $generator = $this->createMock(HydratorCodeGenerator::class);
        $generator
            ->expects($this->once())
            ->method('generate')
            ->with($metadata)
            ->willReturn($code = '<?php class MyClassHydrator {} ');

        $writer = $this->createMock(HydratorCodeWriter::class);
        $writer
            ->expects($this->once())
            ->method('write')
            ->with($metadata, $code);

        $compiler = new Compiler($metadataFactory, $generator, $writer);

        $compiler->compile('My\\Class');
    }
}
