<?php

namespace TSantos\SerializerBundle\Cache;

use Metadata\AdvancedMetadataFactoryInterface;
use PHPUnit\Framework\TestCase;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\SerializerClassCodeGenerator;
use TSantos\Serializer\SerializerClassWriter;

/**
 * Class CacheWarmerTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @group unit
 */
class CacheWarmerTest extends TestCase
{
    private $metadataFactory;
    private $codeGenerator;
    private $classWriter;
    private $warmer;

    public function setUp()
    {
        $this->metadataFactory = $this->createMock(AdvancedMetadataFactoryInterface::class);
        $this->codeGenerator = $this->createMock(SerializerClassCodeGenerator::class);
        $this->classWriter = $this->createMock(SerializerClassWriter::class);
        $this->warmer = new CacheWarmer($this->metadataFactory, $this->codeGenerator, $this->classWriter);
    }

    /** @test */
    public function it_can_warmup_the_cache()
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

        $this->warmer->warmUp('/some/path');
    }

    /** @test */
    public function its_warm_up_should_be_optional()
    {
        $this->assertTrue($this->warmer->isOptional());
    }
}
