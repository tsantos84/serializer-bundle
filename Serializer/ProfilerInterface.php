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

use TSantos\Serializer\Event\Event;

/**
 * Class Stopwatch.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface ProfilerInterface
{
    public function start(Event $event): void;

    public function stop(Event $event): void;

    public function countSerializations(): int;

    public function countDeserializations(): int;

    public function countTotal(): int;

    public function getSerializationDuration(): int;

    public function getDeserializationDuration(): int;

    public function getTotalDuration(): int;
}
