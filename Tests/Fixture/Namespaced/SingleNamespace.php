<?php

/**
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SingleNamespace;

interface Dummy {};

class Bar extends \stdClass implements Dummy
{
}

class Baz
{
}

class Foo {}
