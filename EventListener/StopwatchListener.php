<?php
/**
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\EventListener;

use Symfony\Component\Stopwatch\Stopwatch;
use TSantos\Serializer\AbstractContext;
use TSantos\Serializer\Event\PostDeserializationEvent;
use TSantos\Serializer\Event\PostSerializationEvent;
use TSantos\Serializer\Event\PreDeserializationEvent;
use TSantos\Serializer\Event\PreSerializationEvent;
use TSantos\Serializer\EventDispatcher\EventSubscriberInterface;
use TSantos\Serializer\Events;

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
