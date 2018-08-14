<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests\DataCollector;

use Metadata\Driver\AdvancedDriverInterface;
use Metadata\MetadataFactoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\SerializerBundle\ClassLocator;
use TSantos\SerializerBundle\DataCollector\SerializerCollector;
use TSantos\SerializerBundle\Serializer\ProfilerInterface;
use TSantos\SerializerBundle\TSantosSerializerBundle;

class SerializerCollectorTest extends TestCase
{
    /**
     * @var SerializerCollector
     */
    private $collector;

    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var ClassLocator
     */
    private $classLocator;

    /**
     * @var AdvancedDriverInterface
     */
    private $driver;

    /**
     * @var ProfilerInterface|MockObject
     */
    private $profiler;

    public function setUp()
    {
        $this->metadataFactory = $this->createMock(MetadataFactoryInterface::class);
        $this->classLocator = $this->createMock(ClassLocator::class);
        $this->driver = $this->createMock(AdvancedDriverInterface::class);
        $this->profiler = $this->createMock(ProfilerInterface::class);
        $this->collector = new SerializerCollector($this->metadataFactory, [$this->driver], $this->classLocator, $this->profiler);
    }

    /** @test */
    public function it_should_return_empty_mapping_information()
    {
        $this->driver
            ->method('getAllClassNames')
            ->willReturn([]);

        $this->collector->lateCollect();
        $this->assertSame([], $this->collector->getMappedClasses());
        $this->assertSame([], $this->collector->getAutoMappedClasses());
    }

    /** @test */
    public function it_should_return_mapping_information()
    {
        $m1 = new ClassMetadata(__CLASS__);
        $m2 = new ClassMetadata(TSantosSerializerBundle::class);

        $this->driver
            ->method('getAllClassNames')
            ->willReturn([__CLASS__]);

        $this->metadataFactory
            ->method('getMetadataForClass')
            ->will($this->onConsecutiveCalls($m1, $m2));

        $this->classLocator
            ->method('findAllClasses')
            ->willReturn([TSantosSerializerBundle::class]);

        $this->collector->lateCollect();
        $this->assertCount(1, $this->collector->getMappedClasses());
        $this->assertCount(1, $this->collector->getAutoMappedClasses());
    }

    /** @test */
    public function it_should_return_mapping_information_but_not_repeat_classes_from_explicitly_mapping()
    {
        $m1 = new ClassMetadata(__CLASS__);

        $this->driver
            ->method('getAllClassNames')
            ->willReturn([__CLASS__]);

        $this->metadataFactory
            ->method('getMetadataForClass')
            ->willReturn($m1);

        $this->classLocator
            ->method('findAllClasses')
            ->willReturn([__CLASS__]);

        $this->collector->lateCollect();
        $this->assertCount(1, $this->collector->getMappedClasses());
        $this->assertCount(0, $this->collector->getAutoMappedClasses());
    }

    /**
     * @test
     */
    public function it_should_return_profiler_counting_information()
    {
        $this->driver
            ->expects($this->once())
            ->method('getAllClassNames')
            ->willReturn([]);

        $this->profiler
            ->expects($this->once())
            ->method('countSerializations')
            ->willReturn(10);

        $this->profiler
            ->expects($this->once())
            ->method('countDeserializations')
            ->willReturn(10);

        $this->profiler
            ->expects($this->once())
            ->method('countTotal')
            ->willReturn(20);

        $this->collector->lateCollect();

        $this->assertSame(10, $this->collector->getSerializationCount());
        $this->assertSame(10, $this->collector->getDeserializationCount());
        $this->assertSame(20, $this->collector->getTotalCount());
    }

    /**
     * @test
     */
    public function it_should_return_profiler_timing_information()
    {
        $this->driver
            ->expects($this->once())
            ->method('getAllClassNames')
            ->willReturn([]);

        $this->profiler
            ->expects($this->once())
            ->method('getSerializationDuration')
            ->willReturn(10.);

        $this->profiler
            ->expects($this->once())
            ->method('getDeserializationDuration')
            ->willReturn(10.);

        $this->profiler
            ->expects($this->once())
            ->method('getTotalDuration')
            ->willReturn(20.);

        $this->collector->lateCollect();

        $this->assertSame(10., $this->collector->getSerializationDuration());
        $this->assertSame(10., $this->collector->getDeserializationDuration());
        $this->assertSame(20., $this->collector->getTotalDuration());
    }
}
