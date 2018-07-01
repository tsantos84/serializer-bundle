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
use Symfony\Component\Stopwatch\Stopwatch;
use TSantos\Serializer\DeserializationContext;
use TSantos\Serializer\Event\PostDeserializationEvent;
use TSantos\Serializer\Event\PostSerializationEvent;
use TSantos\Serializer\Event\PreDeserializationEvent;
use TSantos\Serializer\Event\PreSerializationEvent;
use TSantos\Serializer\SerializationContext;
use TSantos\SerializerBundle\EventListener\StopwatchListener;

class StopwatchListenerTest extends TestCase
{
    private $stopwatch;
    private $listener;

    public function setUp()
    {
        $this->stopwatch = $this->createMock(Stopwatch::class);
        $this->listener = new StopwatchListener($this->stopwatch);
    }

    /** @test */
    public function it_should_start_serialization_measuring_when_context_depth_level_is_zero()
    {
        $event = $this->createMock(PreSerializationEvent::class);

        $event
            ->expects($this->once())
            ->method('getContext')
            ->willReturn($context = $this->createMock(SerializationContext::class));

        $context
            ->expects($this->once())
            ->method('getCurrentDepth')
            ->willReturn(0);

        $this->stopwatch
            ->expects($this->once())
            ->method('start')
            ->with('tsantos_serializer');

        $this->listener->startSerialization($event);
    }

    /** @test */
    public function it_should_not_start_serialization__measuring_when_context_depth_level_greater_than_zero()
    {
        $event = $this->createMock(PreSerializationEvent::class);

        $event
            ->expects($this->once())
            ->method('getContext')
            ->willReturn($context = $this->createMock(SerializationContext::class));

        $context
            ->expects($this->once())
            ->method('getCurrentDepth')
            ->willReturn(1);

        $this->stopwatch
            ->expects($this->never())
            ->method('start')
            ->with('tsantos_serializer');

        $this->listener->startSerialization($event);
    }

    /** @test */
    public function it_should_stop_serialization__measuring_when_context_depth_level_is_zero()
    {
        $event = $this->createMock(PostSerializationEvent::class);

        $event
            ->expects($this->once())
            ->method('getContext')
            ->willReturn($context = $this->createMock(SerializationContext::class));

        $context
            ->expects($this->once())
            ->method('getCurrentDepth')
            ->willReturn(0);

        $this->stopwatch
            ->expects($this->once())
            ->method('stop')
            ->with('tsantos_serializer');

        $this->listener->stopSerialization($event);
    }

    /** @test */
    public function it_should_not_serialization__stop_measuring_when_context_depth_level_is_greater_than_zero()
    {
        $event = $this->createMock(PostSerializationEvent::class);

        $event
            ->expects($this->once())
            ->method('getContext')
            ->willReturn($context = $this->createMock(SerializationContext::class));

        $context
            ->expects($this->once())
            ->method('getCurrentDepth')
            ->willReturn(1);

        $this->stopwatch
            ->expects($this->never())
            ->method('stop')
            ->with('tsantos_serializer');

        $this->listener->stopSerialization($event);
    }

    /** @test */
    public function it_should_start_deserialization_measuring_when_context_depth_level_is_zero()
    {
        $event = $this->createMock(PreDeserializationEvent::class);

        $event
            ->expects($this->once())
            ->method('getContext')
            ->willReturn($context = $this->createMock(DeserializationContext::class));

        $context
            ->expects($this->once())
            ->method('getCurrentDepth')
            ->willReturn(0);

        $this->stopwatch
            ->expects($this->once())
            ->method('start')
            ->with('tsantos_serializer');

        $this->listener->startDeserialization($event);
    }

    /** @test */
    public function it_should_not_start_deserialization__measuring_when_context_depth_level_greater_than_zero()
    {
        $event = $this->createMock(PreDeserializationEvent::class);

        $event
            ->expects($this->once())
            ->method('getContext')
            ->willReturn($context = $this->createMock(DeserializationContext::class));

        $context
            ->expects($this->once())
            ->method('getCurrentDepth')
            ->willReturn(1);

        $this->stopwatch
            ->expects($this->never())
            ->method('start')
            ->with('tsantos_serializer');

        $this->listener->startDeserialization($event);
    }

    /** @test */
    public function it_should_stop_deserialization__measuring_when_context_depth_level_is_zero()
    {
        $event = $this->createMock(PostDeserializationEvent::class);

        $event
            ->expects($this->once())
            ->method('getContext')
            ->willReturn($context = $this->createMock(DeserializationContext::class));

        $context
            ->expects($this->once())
            ->method('getCurrentDepth')
            ->willReturn(0);

        $this->stopwatch
            ->expects($this->once())
            ->method('stop')
            ->with('tsantos_serializer');

        $this->listener->stopDeserialization($event);
    }

    /** @test */
    public function it_should_not_deserialization__stop_measuring_when_context_depth_level_is_greater_than_zero()
    {
        $event = $this->createMock(PostDeserializationEvent::class);

        $event
            ->expects($this->once())
            ->method('getContext')
            ->willReturn($context = $this->createMock(DeserializationContext::class));

        $context
            ->expects($this->once())
            ->method('getCurrentDepth')
            ->willReturn(1);

        $this->stopwatch
            ->expects($this->never())
            ->method('stop')
            ->with('tsantos_serializer');

        $this->listener->stopDeserialization($event);
    }
}
