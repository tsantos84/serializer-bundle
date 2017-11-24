<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use TSantos\Bundle\DependencyInjection\Compiler\EncoderPass;
use TSantos\Bundle\DependencyInjection\Compiler\MetadataDriverPass;
use TSantos\Bundle\DependencyInjection\TSantosSerializerExtension;

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
        $container->addCompilerPass(new MetadataDriverPass());
    }

    public function getContainerExtension()
    {
        return new TSantosSerializerExtension();
    }
}
