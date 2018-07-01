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
use TSantos\SerializerBundle\DependencyInjection\Compiler\AddMetadataConfiguratorPass;

/**
 * Class AddMetadataConfiguratorPassTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class AddMetadataConfiguratorPassTest extends TestCase
{
    /** @test */
    public function it_should_replace_the_collection_argument()
    {
        $container = new ContainerBuilder();
        $container
            ->register('tsantos_serializer.configurator_driver')
            ->setArguments([null, []]);

        $container
            ->register('some_metadata_configurator')
            ->addTag('tsantos_serializer.metadata_configurator');

        $compiler = new AddMetadataConfiguratorPass();
        $compiler->process($container);
        $definition = $container->getDefinition('tsantos_serializer.configurator_driver');

        $this->assertSame('some_metadata_configurator', (string) $definition->getArgument(1)[0]);
    }
}
