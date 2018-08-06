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

use TSantos\Serializer\Exception\CircularReferenceException;

/**
 * Class CircularReferenceHandler.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class CircularReferenceHandler
{
    public function __invoke($data, CircularReferenceException $circularReferenceException): string
    {
        $name = method_exists($data, '__toString')
            ? (string) $data
            : spl_object_hash($data);

        return $name;
    }
}
