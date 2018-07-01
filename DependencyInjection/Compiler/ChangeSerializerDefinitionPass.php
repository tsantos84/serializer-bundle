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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use TSantos\Serializer\EventEmitterSerializer;

/**
 * Class ChangeSerializerDefinition.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ChangeSerializerDefinitionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('tsantos_serializer.event_dispatcher')) {
            return;
        }

        $definition = $container->getDefinition('tsantos_serializer.event_dispatcher');

        if (count($definition->getMethodCalls())) {
            $container
                ->getDefinition('tsantos_serializer')
                ->setClass(EventEmitterSerializer::class)
                ->addArgument(new Reference('tsantos_serializer.event_dispatcher'));
        }
    }
}
