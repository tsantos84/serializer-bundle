<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\EventListener;

use Symfony\Component\Stopwatch\Stopwatch;
use TSantos\Serializer\AbstractContext;
use TSantos\Serializer\EventDispatcher\Event\PostDeserializationEvent;
use TSantos\Serializer\EventDispatcher\Event\PostSerializationEvent;
use TSantos\Serializer\EventDispatcher\Event\PreDeserializationEvent;
use TSantos\Serializer\EventDispatcher\Event\PreSerializationEvent;
use TSantos\Serializer\EventDispatcher\EventSubscriberInterface;
use TSantos\Serializer\EventDispatcher\SerializerEvents;

/**
 * Class StopwatchListener
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class StopwatchListener implements EventSubscriberInterface
{
    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * StopwatchListener constructor.
     * @param Stopwatch $stopwatch
     */
    public function __construct(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    public static function getListeners(): array
    {
        return [
            SerializerEvents::PRE_SERIALIZATION => 'startSerialization',
            SerializerEvents::POST_SERIALIZATION => 'stopSerialization',
            SerializerEvents::PRE_DESERIALIZATION => 'startSerialization',
            SerializerEvents::POST_DESERIALIZATION => 'stopSerialization',
        ];
    }

    public function startSerialization(PreSerializationEvent $event): void
    {
        $this->start($event->getContext());
    }

    public function stopSerialization(PostSerializationEvent $event): void
    {
        $this->stop($event->getContext());
    }

    public function startDeserialization(PreDeserializationEvent $event): void
    {
        $this->start($event->getContext());
    }

    public function stopDeserialization(PostDeserializationEvent $event): void
    {
        $this->stop($event->getContext());
    }

    private function start(AbstractContext $context)
    {
        if ($context->getCurrentDepth() === 0) {
            $this->stopwatch->start('serializer');
        }
    }

    private function stop(AbstractContext $context)
    {
        if ($context->getCurrentDepth() === 0) {
            $this->stopwatch->stop('serializer');
        }
    }
}
