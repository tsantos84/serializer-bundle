<?php

namespace TSantos\SerializerBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TSantos\SerializerBundle\DependencyInjection\Compiler\MetadataDriverPass;

/**
 * Class MetadataDriverPassTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @group functional
 */
class MetadataDriverPassTest extends TestCase
{
    /** @test */
    public function it_should_add_method_calls_to_encoder_registry_definition()
    {
        $container = new ContainerBuilder();
        $container->register('tsantos_serializer.metadata_chain_driver');

        $container
            ->register('some_driver_format')
            ->addTag('tsantos_serializer.metadata_driver');

        $compiler = new MetadataDriverPass();
        $compiler->process($container);

        $definition = $container->getDefinition('tsantos_serializer.metadata_chain_driver');
        $this->assertCount(1, $definition->getMethodCalls());
    }
}
