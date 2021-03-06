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
 * Class RemoveStopwatchListenerPass.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class RemoveStopwatchListenerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('tsantos_serializer.stopwatch_listener')) {
            return;
        }

        if (!$container->hasDefinition('debug.stopwatch')) {
            $container->removeDefinition('tsantos_serializer.stopwatch_listener');
        }
    }
}
