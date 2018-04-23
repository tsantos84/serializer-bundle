<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use TSantos\Serializer\SerializerClassLoader;

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

        $strategyDictionary = [
            'never' => SerializerClassLoader::AUTOGENERATE_NEVER,
            'always' => SerializerClassLoader::AUTOGENERATE_ALWAYS,
            'file_not_exists' => SerializerClassLoader::AUTOGENERATE_FILE_NOT_EXISTS,
        ];
        $container->setParameter('tsantos_serializer.class_generate_strategy', $strategyDictionary[$mergedConfig['generate_strategy']]);

        $this->createDir($container->getParameterBag()->resolveValue($mergedConfig['class_path']));
        $this->configMetadataPath($mergedConfig['mapping']['paths'], $container);
        $this->configCache($container, $mergedConfig);
    }

    private function configMetadataPath(array $paths, ContainerBuilder $container)
    {
        $normalizedPaths = [];
        foreach ($paths as $path) {
            $normalizedPaths[$path['namespace']] = $path['path'];
        }
        $container->setParameter('tsantos_serializer.metadata_paths', $normalizedPaths);
    }

    private function configCache(ContainerBuilder $container, array $config)
    {
        $cacheConfig = $config['mapping']['cache'];

        $cacheDefinitionId = sprintf('tsantos_serializer.metadata_%s_cache', $cacheConfig['type']);

        if (!$container->hasDefinition($cacheDefinitionId)) {
            return;
        }

        $container->setParameter('tsantos_serializer.metadata_cache_prefix', $cacheConfig['prefix']);
        if ('file' === $cacheConfig['type']) {
            $container
                ->getDefinition($cacheDefinitionId)
                ->replaceArgument(0, $cacheConfig['path']);
            $this->createDir($container->getParameterBag()->resolveValue($cacheConfig['path']));
        } elseif (isset($cacheConfig['id'])) {
            $container
                ->getDefinition($cacheDefinitionId)
                ->replaceArgument(1, new Reference($cacheConfig['id']));
        } else {
            throw new \InvalidArgumentException('You need to configure the node "mapping.cache.id"');
        }

        $container
            ->getDefinition('tsantos_serializer.metadata_factory')
            ->addMethodCall('setCache', [new Reference($cacheDefinitionId)]);
    }

    public function getAlias()
    {
        return 'tsantos_serializer';
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }

    private function createDir(string $dir)
    {
        if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Could not create directory "%s".', $dir));
        }
    }
}
