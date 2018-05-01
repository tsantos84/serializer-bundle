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
        $dispatcherDefinition = $container->register('tsantos_serializer.event_dispatcher');
        $container
            ->register('some_service')
            ->addTag('tsantos_serializer.event_subscriber');

        $compiler = new EventListenerPass();
        $compiler->process($container);

        $this->assertCount(1, $dispatcherDefinition->getMethodCalls());
    }

    /** @test */
    public function it_should_not_add_listeners_if_the_dispatcher_definition_is_not_present()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('tsantos_serializer.event_dispatcher')
            ->willReturn(false);

        $container
            ->expects($this->never())
            ->method('getDefinition')
            ->with('tsantos_serializer.event_dispatcher');

        $compiler = new EventListenerPass();
        $compiler->process($container);
    }
}
