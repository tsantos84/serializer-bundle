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
    private $tracker;

    /**
     * @var ProfilerListener
     */
    private $listener;

    public function setUp()
    {
        $this->tracker = $this->createMock(ProfilerInterface::class);
        $this->listener = new ProfilerListener($this->tracker);
    }

    /**
     * @test
     */
    public function it_can_track_the_start_of_serialization()
    {
        $this->tracker
            ->expects($this->once())
            ->method('startSerialization');

        $this->listener->startSerialization($this->createMock(PreSerializationEvent::class));
    }

    /**
     * @test
     */
    public function it_can_track_the_end_of_serialization()
    {
        $this->tracker
            ->expects($this->once())
            ->method('finishSerialization');

        $this->listener->stopSerialization($this->createMock(PostSerializationEvent::class));
    }

    /**
     * @test
     */
    public function it_can_track_the_start_of_deserialization()
    {
        $this->tracker
            ->expects($this->once())
            ->method('startDeserialization');

        $this->listener->startDeserialization($this->createMock(PreDeserializationEvent::class));
    }

    /**
     * @test
     */
    public function it_can_track_the_end_of_deserialization()
    {
        $this->tracker
            ->expects($this->once())
            ->method('finishDeserialization');

        $this->listener->stopDeserialization($this->createMock(PostDeserializationEvent::class));
    }
}
