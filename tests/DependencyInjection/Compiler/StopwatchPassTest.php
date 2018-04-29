<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TSantos\SerializerBundle\DependencyInjection\Compiler\StopwatchPass;

/**
 * Class StopwatchPassTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class StopwatchPassTest extends TestCase
{
    /** @test */
    public function it_should_remove_the_stopwatch_listener_in_case_of_stopwatch_service_is_not_defined()
    {
        $container = new ContainerBuilder();
        $container->register('tsantos_serializer.stopwatch_listener');

        $compiler = new StopwatchPass();
        $compiler->process($container);

        $this->assertFalse($container->hasDefinition('tsantos_serializer.stopwatch_listener'));
    }

    /** @test */
    public function it_should_not_remove_the_stopwatch_listener_when_the_service_stopwatch_is_defined()
    {
        $container = new ContainerBuilder();
        $container->register('tsantos_serializer.stopwatch_listener');
        $container->register('debug.stopwatch');

        $compiler = new StopwatchPass();
        $compiler->process($container);

        $this->assertTrue($container->hasDefinition('tsantos_serializer.stopwatch_listener'));
    }
}
