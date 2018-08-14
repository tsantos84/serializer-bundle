<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Serializer;

use Symfony\Component\Stopwatch\Stopwatch as SfStopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;
use TSantos\Serializer\AbstractContext;
use TSantos\Serializer\Event\Event;
use TSantos\Serializer\Event\PostSerializationEvent;
use TSantos\Serializer\Event\PreSerializationEvent;
use TSantos\Serializer\SerializationContext;

/**
 * Class Stopwatch.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
final class Profiler implements ProfilerInterface
{
    /**
     * @var SfStopwatch
     */
    private $stopwatch;

    /**
     * @var StopwatchEvent[]
     */
    private $serializations = [];

    /**
     * @var int
     */
    private $serializeIndex = 0;

    /**
     * @var StopwatchEvent[]
     */
    private $deserializations = [];

    /**
     * @var int
     */
    private $deserializeIndex = 0;

    /**
     * Timer constructor.
     */
    public function __construct()
    {
        $this->stopwatch = new SfStopwatch();
    }

    public function start(Event $event): void
    {
        if ($event instanceof PreSerializationEvent) {
            ++$this->serializeIndex;
            $this->stopwatch->start('serialization_'.$this->serializeIndex);
            return;
        }

        ++$this->deserializeIndex;
        $this->stopwatch->start('deserialization_'.$this->deserializeIndex);
    }

    public function stop(Event $event): void
    {
        if ($event instanceof PostSerializationEvent) {
            $this->serializations[] = $this->stopwatch->stop('serialization_' . $this->serializeIndex);
            --$this->serializeIndex;
            return;
        }

        $this->deserializations[] = $this->stopwatch->stop('deserialization_'.$this->deserializeIndex);
        --$this->deserializeIndex;
    }

    public function countSerializations(): int
    {
        return count($this->serializations);
    }

    public function countDeserializations(): int
    {
        return count($this->deserializations);
    }

    public function countTotal(): int
    {
        return $this->countSerializations() + $this->countDeserializations();
    }

    public function getSerializationDuration(): int
    {
        return array_reduce($this->serializations, function (int $duration, StopwatchEvent $ev) {
            return $ev->getDuration() + $duration;
        }, 0);
    }

    public function getDeserializationDuration(): int
    {
        return array_reduce($this->deserializations, function (int $duration, StopwatchEvent $ev) {
            return $ev->getDuration() + $duration;
        }, 0);
    }

    public function getTotalDuration(): int
    {
        return $this->getSerializationDuration() + $this->getDeserializationDuration();
    }
}
