<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Cache;

use Metadata\AdvancedMetadataFactoryInterface;
use PHPUnit\Framework\TestCase;
use TSantos\Serializer\HydratorCodeGenerator;
use TSantos\Serializer\HydratorCodeWriter;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\SerializerBundle\Service\ClassGenerator;

/**
 * Class ClassGeneratorTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ClassGeneratorTest extends TestCase
{
    private $metadataFactory;
    private $codeGenerator;
    private $classWriter;
    private $generator;

    public function setUp()
    {
        $this->metadataFactory = $this->createMock(AdvancedMetadataFactoryInterface::class);
        $this->codeGenerator = $this->createMock(HydratorCodeGenerator::class);
        $this->classWriter = $this->createMock(HydratorCodeWriter::class);
        $this->generator = new ClassGenerator($this->metadataFactory, $this->codeGenerator, $this->classWriter);
    }

    /** @test */
    public function it_can_generate_the_classes()
    {
        $this->metadataFactory
            ->expects($this->once())
            ->method('getAllClassNames')
            ->willReturn([\stdClass::class]);

        $this->metadataFactory
            ->expects($this->once())
            ->method('getMetadataForClass')
            ->with(\stdClass::class)
            ->willReturn($metadata = $this->createMock(ClassMetadata::class));

        $this->codeGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($metadata)
            ->willReturn($code = 'some/php/code');

        $this->classWriter
            ->expects($this->once())
            ->method('write')
            ->with($metadata, $code);

        $this->generator->generate();
    }

    /** @test */
    public function it_can_count_the_amount_of_metadata_classes()
    {
        $this->metadataFactory
            ->expects($this->once())
            ->method('getAllClassNames')
            ->willReturn([ClassMetadata::class]);

        $this->assertSame(1, $this->generator->count());
    }
}
