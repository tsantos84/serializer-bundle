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

use Metadata\Driver\DriverInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use TSantos\Serializer\EventDispatcher\EventSubscriberInterface;
use TSantos\Serializer\Normalizer\DenormalizerInterface;
use TSantos\Serializer\Normalizer\NormalizerInterface;
use TSantos\Serializer\SerializerClassLoader;

/**
 * Class TSantosSerializerExtension
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class TSantosSerializerExtension extends ConfigurableExtension
{
    /**
     * @param array $mergedConfig
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator([__DIR__ . '/../Resources/config/']));
        $loader->load('services.xml');

        $container->setParameter('tsantos_serializer.debug', $container->getParameterBag()->resolveValue($mergedConfig['debug']));
        $container->setParameter('tsantos_serializer.format', $mergedConfig['format']);

        $container->getDefinition('tsantos_serializer.class_writer')->replaceArgument(0, $mergedConfig['class_path']);

        $strategyDictionary = [
            'never' => SerializerClassLoader::AUTOGENERATE_NEVER,
            'always' => SerializerClassLoader::AUTOGENERATE_ALWAYS,
            'file_not_exists' => SerializerClassLoader::AUTOGENERATE_FILE_NOT_EXISTS,
        ];
        $container->getDefinition('tsantos_serializer.class_loader')->replaceArgument(3, $strategyDictionary[$mergedConfig['generate_strategy']]);

        $this->createDir($container->getParameterBag()->resolveValue($mergedConfig['class_path']));
        $this->configMetadataPath($mergedConfig, $container);
        $this->configCache($container, $mergedConfig);

        // add tags automatically to services
        $container->registerForAutoconfiguration(DriverInterface::class)->addTag('tsantos_serializer.metadata_driver');
        $container->registerForAutoconfiguration(NormalizerInterface::class)->addTag('tsantos_serializer.normalizer');
        $container->registerForAutoconfiguration(DenormalizerInterface::class)->addTag('tsantos_serializer.denormalizer');
        $container->registerForAutoconfiguration(EventSubscriberInterface::class)->addTag('tsantos_serializer.event_subscriber');

        if ($container->getParameter('tsantos_serializer.debug')) {
            $loader->load('debug.xml');
        }
    }

    private function configMetadataPath(array &$config, ContainerBuilder $container)
    {
        $normalizedPaths = [];

        if ($config['mapping']['auto_configure']) {
            $this->configAutoConfiguration($container, $normalizedPaths);
        }

        foreach ($config['mapping']['paths'] as $path) {
            $normalizedPaths[$path['namespace']] = $path['path'];
        }

        $container->getDefinition('tsantos_serializer.metadata_file_locator')->replaceArgument(0, $normalizedPaths);
    }

    private function configAutoConfiguration(ContainerBuilder $container, &$paths)
    {
        $projectDir = $container->getParameter('kernel.project_dir');

        $mappings = [
            'App\\Entity' => $projectDir . '/src/Entity',
            'App\\Document' => $projectDir . '/src/Document',
            'App\\Model' => $projectDir . '/src/Model',
            '' => $projectDir . '/config/serializer' // should be the last item
        ];

        $pathLocator = function () use ($mappings): ?string {
            foreach ($mappings as $namespace => $path) {
                if (is_dir($path)) {
                    return $namespace;
                }
            }
            return null;
        };

        if (is_dir($configPath = $projectDir . '/config/serializer')) {
            if (null !== $namespace = $pathLocator()) {
                $paths[$namespace] = $configPath;
                return;
            }
        }

        if (null !== $namespace = $pathLocator()) {
            $paths[$namespace] = $mappings[$namespace];
        }
    }

    private function configCache(ContainerBuilder $container, array $config)
    {
        $cacheConfig = $config['mapping']['cache'];

        $cacheDefinitionId = sprintf('tsantos_serializer.metadata_%s_cache', $cacheConfig['type']);

        if ('file' === $cacheConfig['type']) {
            $container
                ->getDefinition($cacheDefinitionId)
                ->replaceArgument(0, $cacheConfig['path']);
            $this->createDir($container->getParameterBag()->resolveValue($cacheConfig['path']));
        } elseif (isset($cacheConfig['id'])) {
            $container->getDefinition($cacheDefinitionId)->replaceArgument(0, $cacheConfig['prefix']);
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
