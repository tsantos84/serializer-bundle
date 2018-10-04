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
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AddCodeDecoratorPass.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class AddCodeDecoratorPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container)
    {
        $references = [];
        foreach ($this->findAndSortTaggedServices('tsantos_serializer.code_decorator', $container) as $serviceId) {
            $references[] = new Reference($serviceId);
        }

        $container
            ->getDefinition('tsantos_serializer.hydrator_code_generator')
            ->setArgument(2, $references);
    }
}
