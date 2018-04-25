<?php

namespace TSantos\SerializerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use TSantos\Serializer\EventEmitterSerializer;

/**
 * Class EventListenerPass
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class EventListenerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('tsantos_serializer.event_dispatcher')) {
            return;
        }

        $definition = $container->getDefinition('tsantos_serializer.event_dispatcher');

        foreach ($container->findTaggedServiceIds('tsantos_serializer.event_subscriber') as $serviceId => $tags) {
            $definition->addMethodCall('addSubscriber', [new Reference($serviceId)]);
        }

        if (count($definition->getMethodCalls())) {
            $container
                ->getDefinition('tsantos_serializer')
                ->setClass(EventEmitterSerializer::class)
                ->addArgument(new Reference('tsantos_serializer.event_dispatcher'));
        }
    }
}
