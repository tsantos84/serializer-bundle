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
use TSantos\SerializerBundle\DependencyInjection\Compiler\AddCodeDecoratorPass;

/**
 * Class AddCodeDecoratorPassTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class AddCodeDecoratorPassTest extends TestCase
{
    /** @test */
    public function it_should_configure_the_hydrator_code_generator_parameters()
    {
        $container = new ContainerBuilder();
        $container->register('tsantos_serializer.hydrator_code_generator');

        $container
            ->register('some_code_generator')
            ->setArgument(2, [])
            ->addTag('tsantos_serializer.code_decorator');

        $compiler = new AddCodeDecoratorPass();
        $compiler->process($container);

        $definition = $container->getDefinition('tsantos_serializer.hydrator_code_generator');
        $parameters = $definition->getArgument(2);
        $this->assertCount(1, $parameters);
    }
}
