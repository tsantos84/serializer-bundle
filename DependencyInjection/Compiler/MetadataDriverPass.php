<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class MetadataDriverPass
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class MetadataDriverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('tsantos_serializer.metadata_chain_driver')) {
            return;
        }

        $definition = $container->getDefinition('tsantos_serializer.metadata_chain_driver');

        foreach ($container->findTaggedServiceIds('tsantos_serializer.metadata_driver') as $id => $serviceId) {
            $definition->addMethodCall('addDriver', [new Reference($id)]);
        }
    }
}
