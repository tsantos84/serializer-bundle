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
use TSantos\SerializerBundle\DependencyInjection\Compiler\AddTwigPathPass;

/**
 * Class TwigPassTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class TwigPassTest extends TestCase
{
    /** @test */
    public function it_should_add_path_to_hydrator_template()
    {
        $container = new ContainerBuilder();
        $container->register('twig.loader.native_filesystem');

        $compiler = new AddTwigPathPass();
        $compiler->process($container);

        $definition = $container->getDefinition('twig.loader.native_filesystem');
        $this->assertCount(1, $calls = $definition->getMethodCalls());
        $this->assertEquals('addPath', $calls[0][0]);
        $this->assertStringEndsWith('/serializer/src/Resources/templates', $calls[0][1][0]);
    }

    /** @test */
    public function it_should_not_add_path_to_hydrator_template_if_the_twig_loader_is_not_registered()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('twig.loader.native_filesystem')
            ->willReturn(false);

        $container
            ->expects($this->never())
            ->method('getDefinition')
            ->with('twig.loader.native_filesystem');

        $compiler = new AddTwigPathPass();
        $compiler->process($container);
    }
}
