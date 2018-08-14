<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use TSantos\Serializer\Event\PostDeserializationEvent;
use TSantos\Serializer\Event\PostSerializationEvent;
use TSantos\Serializer\Event\PreDeserializationEvent;
use TSantos\Serializer\Event\PreSerializationEvent;
use TSantos\SerializerBundle\EventListener\ProfilerListener;
use TSantos\SerializerBundle\Serializer\ProfilerInterface;

/**
 * Class ProfilerListenerTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ProfilerListenerTest extends TestCase
{
    /**
     * @var ProfilerInterface
     */
    private $profiler;

    /**
     * @var ProfilerListener
     */
    private $listener;

    public function setUp()
    {
        $this->profiler = $this->createMock(ProfilerInterface::class);
        $this->listener = new ProfilerListener($this->profiler);
    }

    /**
     * @test
     */
    public function it_can_track_the_start_of_serialization()
    {
        $event = $this->createMock(PreSerializationEvent::class);

        $this->profiler
            ->expects($this->once())
            ->method('start')
            ->with($event);

        $this->listener->startSerialization($event);
    }

    /**
     * @test
     */
    public function it_can_track_the_end_of_serialization()
    {
        $event = $this->createMock(PostSerializationEvent::class);

        $this->profiler
            ->expects($this->once())
            ->method('stop')
            ->with($event);

        $this->listener->stopSerialization($event);
    }

    /**
     * @test
     */
    public function it_can_track_the_start_of_deserialization()
    {
        $event = $this->createMock(PreDeserializationEvent::class);

        $this->profiler
            ->expects($this->once())
            ->method('start')
            ->with($event);

        $this->listener->startDeserialization($event);
    }

    /**
     * @test
     */
    public function it_can_track_the_end_of_deserialization()
    {
        $event = $this->createMock(PostDeserializationEvent::class);

        $this->profiler
            ->expects($this->once())
            ->method('stop')
            ->with($event);

        $this->listener->stopDeserialization($event);
    }
}
