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

/**
 * Class TwigPass.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class AddTwigPathPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('twig.loader.native_filesystem')) {
            return;
        }

        $path = $container->getParameter('kernel.project_dir').'/vendor/tsantos/serializer/src/Resources/templates';

        $container
            ->getDefinition('twig.loader.native_filesystem')
            ->addMethodCall('addPath', [$path]);
    }
}
