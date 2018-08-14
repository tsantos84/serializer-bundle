<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\EventListener;

use TSantos\Serializer\Event\PostDeserializationEvent;
use TSantos\Serializer\Event\PostSerializationEvent;
use TSantos\Serializer\Event\PreDeserializationEvent;
use TSantos\Serializer\Event\PreSerializationEvent;
use TSantos\Serializer\EventDispatcher\EventSubscriberInterface;
use TSantos\Serializer\Events;
use TSantos\SerializerBundle\Serializer\Profiler;
use TSantos\SerializerBundle\Serializer\ProfilerInterface;

/**
 * Class ProfilerListener.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ProfilerListener implements EventSubscriberInterface
{
    /**
     * @var ProfilerInterface
     */
    private $profiler;

    /**
     * SerializerOperationsListener constructor.
     *
     * @param ProfilerInterface $profiler
     */
    public function __construct(ProfilerInterface $profiler = null)
    {
        $this->profiler = $profiler ?? new Profiler();
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getListeners(): array
    {
        return [
            Events::PRE_SERIALIZATION => 'startSerialization',
            Events::POST_SERIALIZATION => 'stopSerialization',
            Events::PRE_DESERIALIZATION => 'startSerialization',
            Events::POST_DESERIALIZATION => 'stopSerialization',
        ];
    }

    public function startSerialization(PreSerializationEvent $event): void
    {
        $this->profiler->start($event);
    }

    public function stopSerialization(PostSerializationEvent $event): void
    {
        $this->profiler->stop($event);
    }

    public function startDeserialization(PreDeserializationEvent $event): void
    {
        $this->profiler->start($event);
    }

    public function stopDeserialization(PostDeserializationEvent $event): void
    {
        $this->profiler->stop($event);
    }
}
