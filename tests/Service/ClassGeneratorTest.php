<?php

namespace TSantos\SerializerBundle\Cache;

use Metadata\AdvancedMetadataFactoryInterface;
use PHPUnit\Framework\TestCase;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\SerializerClassCodeGenerator;
use TSantos\Serializer\SerializerClassWriter;
use TSantos\SerializerBundle\Service\ClassGenerator;

/**
 * Class ClassGeneratorTest
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
        $this->codeGenerator = $this->createMock(SerializerClassCodeGenerator::class);
        $this->classWriter = $this->createMock(SerializerClassWriter::class);
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

        $this->assertEquals(1, $this->generator->count());
    }
}
