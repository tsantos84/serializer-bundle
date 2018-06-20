<?php
/**
 * This file is part of the TSantos Serializer Bundle package.
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
 * Class AddMetadataConfiguratorPass
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class AddMetadataConfiguratorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $configurators = [];
        foreach ($container->findTaggedServiceIds('tsantos_serializer.metadata_configurator') as $id => $tags) {
            $configurators[] = new Reference($id);
        }

        $container
            ->getDefinition('tsantos_serializer.configurator_driver')
            ->replaceArgument(1, $configurators);
    }
}
