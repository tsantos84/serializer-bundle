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
use TSantos\SerializerBundle\DependencyInjection\Compiler\EncoderPass;
use TSantos\SerializerBundle\DependencyInjection\Compiler\EventListenerPass;
use TSantos\SerializerBundle\DependencyInjection\Compiler\MetadataDriverPass;
use TSantos\SerializerBundle\DependencyInjection\Compiler\NormalizerPass;
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
        $container->addCompilerPass(new EncoderPass());
        $container->addCompilerPass(new EventListenerPass());
        $container->addCompilerPass(new MetadataDriverPass());
        $container->addCompilerPass(new NormalizerPass());
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new TSantosSerializerExtension();
        }

        return $this->extension;
    }
}
