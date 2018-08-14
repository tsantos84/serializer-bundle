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

/**
 * Class Stopwatch.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
interface ProfilerInterface
{
    public function startSerialization(): void;

    public function finishSerialization(): void;

    public function startDeserialization(): void;

    public function finishDeserialization(): void;

    public function countSerializations(): int;

    public function countDeserializations(): int;

    public function countTotal(): int;

    public function getSerializationDuration(): int;

    public function getDeserializationDuration(): int;

    public function getTotalDuration(): int;
}
