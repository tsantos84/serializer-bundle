<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TSantos\Serializer\EventEmitterSerializer;
use TSantos\Serializer\Serializer;
use TSantos\SerializerBundle\DependencyInjection\Compiler\ChangeSerializerDefinitionPass;

/**
 * Class ChangeSerializerDefinitionPassTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ChangeSerializerDefinitionPassTest extends TestCase
{
    /** @test */
    public function it_should_change_the_serializer_definition_if_there_are_method_calls_to_event_dispatcher_service()
    {
        $container = new ContainerBuilder();
        $serializerDefinition = $container->register('tsantos_serializer');
        $container->register('tsantos_serializer.event_dispatcher')->addMethodCall('addSubscriber');

        $compiler = new ChangeSerializerDefinitionPass();
        $compiler->process($container);

        $this->assertSame(EventEmitterSerializer::class, $serializerDefinition->getClass());
        $this->assertSame('tsantos_serializer.event_dispatcher', (string) $serializerDefinition->getArgument(0));
    }

    /** @test */
    public function it_should_not_change_the_serializer_definition_if_there_are_no_method_calls_to_event_dispatcher_service()
    {
        $container = new ContainerBuilder();
        $serializerDefinition = $container->register('tsantos_serializer', Serializer::class);
        $container->register('tsantos_serializer.event_dispatcher');

        $compiler = new ChangeSerializerDefinitionPass();
        $compiler->process($container);

        $this->assertSame(Serializer::class, $serializerDefinition->getClass());
        $this->assertCount(0, $serializerDefinition->getArguments());
    }

    /** @test */
    public function it_should_not_change_the_serializer_definition_if_event_dispatcher_service_is_not_present()
    {
        $container = new ContainerBuilder();
        $serializerDefinition = $container->register('tsantos_serializer', Serializer::class);

        $compiler = new ChangeSerializerDefinitionPass();
        $compiler->process($container);

        $this->assertSame(Serializer::class, $serializerDefinition->getClass());
        $this->assertCount(0, $serializerDefinition->getArguments());
    }
}
