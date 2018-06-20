<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use TSantos\SerializerBundle\DependencyInjection\Compiler\AddMetadataConfiguratorPass;
use TSantos\SerializerBundle\DependencyInjection\Compiler\AddTwigPathPass;
use TSantos\SerializerBundle\DependencyInjection\Compiler\ChangeSerializerDefinitionPass;
use TSantos\SerializerBundle\DependencyInjection\Compiler\ConfigureEncoderPass;
use TSantos\SerializerBundle\DependencyInjection\Compiler\AddEventListenerPass;
use TSantos\SerializerBundle\DependencyInjection\Compiler\AddMetadataDriverPass;
use TSantos\SerializerBundle\DependencyInjection\Compiler\AddNormalizerPass;
use TSantos\SerializerBundle\DependencyInjection\Compiler\RemoveStopwatchListenerPass;
use TSantos\SerializerBundle\DependencyInjection\TSantosSerializerExtension;

/**
 * Class TSantosSerializerBundle
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class TSantosSerializerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RemoveStopwatchListenerPass());
        $container->addCompilerPass(new AddMetadataConfiguratorPass());
        $container->addCompilerPass(new AddTwigPathPass());
        $container->addCompilerPass(new ConfigureEncoderPass());
        $container->addCompilerPass(new AddEventListenerPass());
        $container->addCompilerPass(new AddMetadataDriverPass());
        $container->addCompilerPass(new AddNormalizerPass());
        $container->addCompilerPass(new ChangeSerializerDefinitionPass());
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new TSantosSerializerExtension();
        }

        return $this->extension;
    }
}
