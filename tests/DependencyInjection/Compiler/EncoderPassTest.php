<?php

namespace TSantos\SerializerBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TSantos\SerializerBundle\DependencyInjection\Compiler\EncoderPass;

/**
 * Class EventListenerPassTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @group functional
 */
class EncoderPassTest extends TestCase
{
    /** @test */
    public function it_should_rewrite_the_definition_of_encoders()
    {
        $container = new ContainerBuilder();

        $container->setParameter('tsantos_serializer.format', 'json');
        $container->register('tsantos_serializer')->setArguments([null, null]);

        $container
            ->register('some_encoder')
            ->setClass('SomeClass')
            ->addTag('tsantos_serializer.encoder', ['format' => 'json']);

        $compiler = new EncoderPass();
        $compiler->process($container);

        $definition = $container->getDefinition('tsantos_serializer');
        $encoderReference = $definition->getArgument(1);
        $this->assertEquals('some_encoder', $encoderReference);
    }
}
