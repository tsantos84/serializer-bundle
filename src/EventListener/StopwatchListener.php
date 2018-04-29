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
use TSantos\Serializer\EventDispatcher\Event\PostSerializationEvent;
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
    public function __construct(?Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    public static function getListeners(): array
    {
        return [
            SerializerEvents::PRE_SERIALIZATION => 'startSerialization',
            SerializerEvents::POST_SERIALIZATION => 'stopSerialization',
        ];
    }

    public function startSerialization(PreSerializationEvent $event): void
    {
        if (null === $this->stopwatch) {
            return;
        }

        if ($event->getContext()->getCurrentDepth() > 0) {
            return;
        }

        $this->stopwatch->start('serializer');
    }

    public function stopSerialization(PostSerializationEvent $event): void
    {
        if (null === $this->stopwatch) {
            return;
        }

        if ($event->getContext()->getCurrentDepth() > 0) {
            return;
        }

        $this->stopwatch->stop('serializer');
    }
}
