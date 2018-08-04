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
use TSantos\Serializer\Metadata\Driver\XmlDriver;
use TSantos\SerializerBundle\DependencyInjection\Compiler\AddMetadataDriverPass;

/**
 * Class AddMetadataDriverPassTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class AddMetadataDriverPassTest extends TestCase
{
    /** @test */
    public function it_should_add_method_calls_to_driver_chain_definition()
    {
        $container = new ContainerBuilder();
        $container->register('tsantos_serializer.metadata_chain_driver');

        $container
            ->register('some_driver_format')
            ->addTag('tsantos_serializer.metadata_driver');

        $compiler = new AddMetadataDriverPass();
        $compiler->process($container);

        $definition = $container->getDefinition('tsantos_serializer.metadata_chain_driver');
        $this->assertCount(1, $definition->getMethodCalls());
    }

    /** @test */
    public function it_should_add_a_non_empty_advanced_driver_array_to_data_collector()
    {
        $container = new ContainerBuilder();
        $container
            ->register('tsantos_serializer.xml_driver', XmlDriver::class)
            ->addTag('tsantos_serializer.metadata_driver');

        $container
            ->register('tsantos_serializer.metadata_chain_driver');

        $container
            ->register('tsantos_serializer.data_collector')
            ->setArguments([null, []]);

        $compiler = new AddMetadataDriverPass();
        $compiler->process($container);

        $definition = $container->getDefinition('tsantos_serializer.data_collector');
        $this->assertCount(1, $definition->getArgument(1));
    }
}
