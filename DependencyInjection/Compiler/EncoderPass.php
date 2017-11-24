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
 * Class EncoderPass
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class EncoderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('tsantos_serializer.encoder_registry')) {
            return;
        }

        $definition = $container->getDefinition('tsantos_serializer.encoder_registry');

        foreach ($container->findTaggedServiceIds('tsantos_serializer.encoder') as $id => $serviceId) {
            $definition->addMethodCall('add', [new Reference($id)]);
        }
    }
}
