<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\DependencyInjection\Compiler;

use Metadata\Driver\AdvancedDriverInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AddMetadataDriverPass.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class AddMetadataDriverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('tsantos_serializer.metadata_chain_driver');
        $advancedDrivers = [];

        foreach ($container->findTaggedServiceIds('tsantos_serializer.metadata_driver') as $id => $serviceId) {
            $reference = new Reference($id);
            $definition->addMethodCall('addDriver', [$reference]);

            $driver = $container->getDefinition($id);
            $ref = new \ReflectionClass($driver->getClass());

            if ($ref->implementsInterface(AdvancedDriverInterface::class)) {
                $advancedDrivers[] = $reference;
            }
        }

        $container
            ->getDefinition('tsantos_serializer.data_collector')
            ->replaceArgument(1, $advancedDrivers);
    }
}
