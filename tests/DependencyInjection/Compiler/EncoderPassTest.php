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
    public function it_should_add_method_calls_to_encoder_registry_definition()
    {
        $container = new ContainerBuilder();
        $container->register('tsantos_serializer.encoder_registry');

        $container
            ->register('some_encoder')
            ->addTag('tsantos_serializer.encoder');

        $compiler = new EncoderPass();
        $compiler->process($container);

        $definition = $container->getDefinition('tsantos_serializer.encoder_registry');
        $this->assertCount(1, $definition->getMethodCalls());
    }
}
