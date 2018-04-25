<?php

namespace TSantos\SerializerBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TSantos\Serializer\EventEmitterSerializer;
use TSantos\SerializerBundle\DependencyInjection\Compiler\EventListenerPass;

/**
 * Class CompilerTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @group functional
 */
class EventListenerPassTest extends TestCase
{
    /** @test */
    public function it_should_add_method_calls_to_event_dispatcher_definition()
    {
        $container = new ContainerBuilder();
        $serializerDefinition = $container->register('tsantos_serializer');
        $dispatcherDefinition = $container->register('tsantos_serializer.event_dispatcher');
        $container
            ->register('some_service')
            ->addTag('tsantos_serializer.event_subscriber');

        $compiler = new EventListenerPass();
        $compiler->process($container);

        $this->assertCount(1, $dispatcherDefinition->getMethodCalls());
        $this->assertEquals(EventEmitterSerializer::class, $serializerDefinition->getClass());
        $this->assertEquals('tsantos_serializer.event_dispatcher', (string)$serializerDefinition->getArgument(0));
    }
}
