<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * Class TSantosSerializerExtension
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class TSantosSerializerExtension extends ConfigurableExtension
{
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator([__DIR__ . '/../Resources/config/']));
        $loader->load('services.xml');

        $container->setParameter('tsantos_serializer.debug', $mergedConfig['debug']);
        $container->setParameter('tsantos_serializer.class_path', $mergedConfig['class_path']);
        $container->setParameter('tsantos_serializer.class_generate_strategy', $mergedConfig['generate_strategy']);

        if ('file' === $mergedConfig['mapping']['cache']['type']) {
            $container
                ->getDefinition('tsantos_serializer.metadata_file_cache')
                ->replaceArgument(0, $mergedConfig['mapping']['cache']['path']);

            $container
                ->getDefinition('tsantos_serializer.metadata_factory')
                ->addMethodCall('setCache', [new Reference('tsantos_serializer.metadata_file_cache')]);
        }

        $normalizedPaths = [];

        foreach ($mergedConfig['mapping']['paths'] as $path) {
            $normalizedPaths[$path['namespace']] = $path['path'];
        }

        $container->setParameter('tsantos_serializer.metadata_paths', $normalizedPaths);
    }

    public function getAlias()
    {
        return 'tsantos_serializer';
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }
}
