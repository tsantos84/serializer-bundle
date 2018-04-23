<?php

namespace TSantos\SerializerBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
        $container
            ->register('tsantos_serializer.event_dispatcher')
            ->addTag('tsantos_serializer.event_subscriber');

        $compiler = new EventListenerPass();
        $compiler->process($container);

        $definition = $container->getDefinition('tsantos_serializer.event_dispatcher');
        $this->assertCount(1, $definition->getMethodCalls());
    }
}
