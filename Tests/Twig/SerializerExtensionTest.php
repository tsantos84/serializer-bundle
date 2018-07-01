<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests\Twig;

use PHPUnit\Framework\TestCase;
use TSantos\Serializer\SerializerInterface;
use TSantos\SerializerBundle\Twig\SerializerExtension;

class SerializerExtensionTest extends TestCase
{
    /** @test */
    public function it_can_filter_an_object()
    {
        $data = new \stdClass();
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($data)
            ->willReturn('{"foo":"bar"}');

        $extension = new SerializerExtension($serializer);

        $twig = new \Twig_Environment(new \Twig_Loader_Array(['page' => '{{ data|serialize }}']));
        $twig->addExtension($extension);

        $result = $twig->render('page', ['data' => $data]);

        $this->assertSame('{"foo":"bar"}', $result);
    }
}
