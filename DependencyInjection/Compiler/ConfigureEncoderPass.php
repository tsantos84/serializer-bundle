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

/**
 * Class ConfigureEncoderPass.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ConfigureEncoderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $ids = [];

        foreach ($container->findTaggedServiceIds('tsantos_serializer.encoder') as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['format'])) {
                    throw new \InvalidArgumentException('The tag "tsantos_serializer.encoder" should have the attribute "format"');
                }
                $ids[$tag['format']] = $id;
            }
        }

        $format = $container->getParameter('tsantos_serializer.format');

        if (!isset($ids[$format])) {
            throw new \InvalidArgumentException('There is no encoder able to handle "'.$format.'" format');
        }

        $container
            ->getDefinition('tsantos_serializer')
            ->replaceArgument(0, new Reference($ids[$format]));
    }
}
