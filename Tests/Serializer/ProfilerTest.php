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

        $profiler->startSerialization();
        $profiler->startSerialization();
        $profiler->startSerialization();
        sleep(1);
        $profiler->finishSerialization();
        $profiler->finishSerialization();
        $profiler->finishSerialization();

        $this->assertSame(3, $profiler->countSerializations());
        $this->assertGreaterThan(0, $profiler->getSerializationDuration());
    }

    /**
     * @test
     */
    public function it_can_count_deserialization_operations()
    {
        $stopwatch = new Profiler();

        $stopwatch->startDeserialization();
        $stopwatch->startDeserialization();
        $stopwatch->startDeserialization();
        sleep(1);
        $stopwatch->finishDeserialization();
        $stopwatch->finishDeserialization();
        $stopwatch->finishDeserialization();

        $this->assertSame(3, $stopwatch->countDeserializations());
        $this->assertGreaterThan(0, $stopwatch->getDeserializationDuration());
    }

    /**
     * @test
     */
    public function it_can_count_total_operations()
    {
        $stopwatch = new Profiler();

        $stopwatch->startSerialization();
        $stopwatch->startDeserialization();
        sleep(1);
        $stopwatch->finishSerialization();
        $stopwatch->finishDeserialization();

        $this->assertSame(2, $stopwatch->countTotal());
        $this->assertGreaterThan(0, $stopwatch->getTotalDuration());
    }
}
