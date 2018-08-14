<?php

/**
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests\Stopwatch;

use PHPUnit\Framework\TestCase;
use TSantos\Serializer\Event\PostDeserializationEvent;
use TSantos\Serializer\Event\PostSerializationEvent;
use TSantos\Serializer\Event\PreDeserializationEvent;
use TSantos\Serializer\Event\PreSerializationEvent;
use TSantos\SerializerBundle\Serializer\Profiler;

/**
 * Class StopwatchTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ProfilerTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_count_serialization_operations()
    {
        $profiler = new Profiler();
        $preEvent = $this->createMock(PreSerializationEvent::class);
        $postEvent = $this->createMock(PostSerializationEvent::class);

        $profiler->start($preEvent);
        $profiler->start($preEvent);
        $profiler->start($preEvent);
        sleep(1);
        $profiler->stop($postEvent);
        $profiler->stop($postEvent);
        $profiler->stop($postEvent);

        $this->assertSame(3, $profiler->countSerializations());
        $this->assertGreaterThan(0, $profiler->getSerializationDuration());
    }

    /**
     * @test
     */
    public function it_can_count_deserialization_operations()
    {
        $preEvent = $this->createMock(PreDeserializationEvent::class);
        $postEvent = $this->createMock(PostDeserializationEvent::class);
        $stopwatch = new Profiler();

        $stopwatch->start($preEvent);
        $stopwatch->start($preEvent);
        $stopwatch->start($preEvent);
        sleep(1);
        $stopwatch->stop($postEvent);
        $stopwatch->stop($postEvent);
        $stopwatch->stop($postEvent);

        $this->assertSame(3, $stopwatch->countDeserializations());
        $this->assertGreaterThan(0, $stopwatch->getDeserializationDuration());
    }

    /**
     * @test
     */
    public function it_can_count_total_operations()
    {
        $stopwatch = new Profiler();

        $stopwatch->start($this->createMock(PreSerializationEvent::class));
        $stopwatch->start($this->createMock(PreDeserializationEvent::class));
        sleep(1);
        $stopwatch->stop($this->createMock(PostSerializationEvent::class));
        $stopwatch->stop($this->createMock(PostDeserializationEvent::class));

        $this->assertSame(2, $stopwatch->countTotal());
        $this->assertGreaterThan(0, $stopwatch->getTotalDuration());
    }
}
