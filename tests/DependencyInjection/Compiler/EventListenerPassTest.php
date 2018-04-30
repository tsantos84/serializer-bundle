<?php
/**
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
use TSantos\SerializerBundle\DependencyInjection\Compiler\EventListenerPass;

/**
 * Class CompilerTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
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

    /** @test */
    public function it_should_not_add_listeners_if_the_dispatcher_definition_is_not_present()
    {
        $container = new ContainerBuilder();
        $serializerDefinition = $container->register('tsantos_serializer', Serializer::class);
        $container
            ->register('some_service')
            ->addTag('tsantos_serializer.event_subscriber');

        $compiler = new EventListenerPass();
        $compiler->process($container);

        $this->assertEquals(Serializer::class, $serializerDefinition->getClass());
    }
}
