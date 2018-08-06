<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests\Serializer;

use PHPUnit\Framework\TestCase;
use TSantos\Serializer\Exception\CircularReferenceException;
use TSantos\SerializerBundle\Serializer\CircularReferenceHandler;

class CircularReferenceHandlerTest extends TestCase
{
    /** @test */
    public function it_should_print_the_object_name()
    {
        $handler = new CircularReferenceHandler();

        $data = new class() {
            public function __toString()
            {
                return 'foo';
            }
        };

        $name = $handler($data, new CircularReferenceException());
        $this->assertSame('foo', $name);
    }

    /** @test */
    public function it_should_print_the_object_hash()
    {
        $handler = new CircularReferenceHandler();
        $data = new class() {
        };
        $name = $handler($data, new CircularReferenceException());
        $this->assertSame(spl_object_hash($data), $name);
    }
}
