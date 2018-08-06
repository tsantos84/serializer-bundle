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
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\SerializerBundle\ClassLocator;
use TSantos\SerializerBundle\DataCollector\SerializerCollector;
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

    private $request;
    private $response;

    public function setUp()
    {
        $this->metadataFactory = $this->createMock(MetadataFactoryInterface::class);
        $this->classLocator = $this->createMock(ClassLocator::class);
        $this->driver = $this->createMock(AdvancedDriverInterface::class);
        $this->request = $this->createMock(Request::class);
        $this->response = $this->createMock(Response::class);
        $this->collector = new SerializerCollector($this->metadataFactory, [$this->driver], $this->classLocator);
    }

    /** @test */
    public function it_should_return_empty_mapping_information()
    {
        $this->driver
            ->method('getAllClassNames')
            ->willReturn([]);

        $this->collector->collect($this->request, $this->response);
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

        $this->collector->collect($this->request, $this->response);
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

        $this->collector->collect($this->request, $this->response);
        $this->assertCount(1, $this->collector->getMappedClasses());
        $this->assertCount(0, $this->collector->getAutoMappedClasses());
    }
}
