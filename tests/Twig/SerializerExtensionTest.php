<?php

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

        $this->assertEquals('{"foo":"bar"}', $result);
    }
}
