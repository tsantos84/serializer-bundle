<?php

namespace TSantos\SerializerBundle\Cache;

use Metadata\AdvancedMetadataFactoryInterface;
use PHPUnit\Framework\TestCase;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\SerializerClassCodeGenerator;
use TSantos\Serializer\SerializerClassWriter;
use TSantos\SerializerBundle\Service\ClassGenerator;

/**
 * Class CacheWarmerTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @group unit
 */
class CacheWarmerTest extends TestCase
{
    private $classGenerator;
    private $warmer;

    public function setUp()
    {
        $this->classGenerator = $this->createMock(ClassGenerator::class);
        $this->warmer = new CacheWarmer($this->classGenerator);
    }

    /** @test */
    public function it_can_warm_up_the_cache()
    {
        $this->classGenerator
            ->expects($this->once())
            ->method('generate');

        $this->warmer->warmUp('/some/path');
    }

    /** @test */
    public function its_warm_up_should_be_optional()
    {
        $this->assertTrue($this->warmer->isOptional());
    }
}