<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\DependencyInjection;

use Doctrine\Common\Annotations\AnnotationReader;
use Metadata\Driver\DriverInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Yaml;
use TSantos\Serializer\EventDispatcher\EventSubscriberInterface;
use TSantos\Serializer\HydratorCompiler;
use TSantos\Serializer\Metadata\ConfiguratorInterface;
use TSantos\Serializer\Normalizer\DenormalizerInterface;
use TSantos\Serializer\Normalizer\NormalizerInterface;
use TSantos\Serializer\SerializerAwareInterface;

/**
 * Class TSantosSerializerExtension.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class TSantosSerializerExtension extends ConfigurableExtension
{
    /**
     * @param array            $mergedConfig
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator([__DIR__.'/../Resources/config/']));
        $loader->load('services.xml');
        $loader->load('hydrator.xml');
        $loader->load('metadata.xml');

        $debug = $container->getParameterBag()->resolveValue($mergedConfig['debug']);

        $container->setParameter('tsantos_serializer.format', $mergedConfig['format']);

        $container
            ->getDefinition('tsantos_serializer.extraction_decorator')
            ->setArgument(1, $mergedConfig['enable_property_grouping']);

        $container
            ->getDefinition('tsantos_serializer.hydration_decorator')
            ->setArgument(1, $mergedConfig['enable_property_grouping']);

        if (false === $mergedConfig['enable_property_grouping']) {
            $container->removeDefinition('tsantos_serializer.exposed_keys_decorator');
        }

        $strategyDictionary = [
            'never' => HydratorCompiler::AUTOGENERATE_NEVER,
            'always' => HydratorCompiler::AUTOGENERATE_ALWAYS,
            'file_not_exists' => HydratorCompiler::AUTOGENERATE_FILE_NOT_EXISTS,
        ];

        $container
            ->getDefinition('tsantos_serializer.configuration')
            ->setArguments([
                $mergedConfig['hydrator_namespace'],
                $mergedConfig['hydrator_path'],
                $strategyDictionary[$mergedConfig['generation_strategy']],
                $mergedConfig['enable_max_depth_check'],
            ]);

        $container
            ->getDefinition('tsantos_serializer.ensure_production_settings_command')
            ->replaceArgument(0, $debug)
            ->replaceArgument(1, $strategyDictionary[$mergedConfig['generation_strategy']]);

        $this->createDir($container->getParameterBag()->resolveValue($mergedConfig['hydrator_path']));
        $this->configMetadataPath($mergedConfig, $container);
        $this->configCache($container, $mergedConfig);

        $container->setParameter('tsantos_serializer.include_dir', $mergedConfig['include_dir']);
        $container->setParameter('tsantos_serializer.exclude_dir', $mergedConfig['exclude_dir']);

        // add tags automatically to services
        $container->registerForAutoconfiguration(DriverInterface::class)->addTag('tsantos_serializer.metadata_driver');
        $container->registerForAutoconfiguration(NormalizerInterface::class)->addTag('tsantos_serializer.normalizer');
        $container->registerForAutoconfiguration(DenormalizerInterface::class)->addTag('tsantos_serializer.denormalizer');
        $container->registerForAutoconfiguration(EventSubscriberInterface::class)->addTag('tsantos_serializer.event_subscriber');
        $container->registerForAutoconfiguration(ConfiguratorInterface::class)->addTag('tsantos_serializer.metadata_configurator');

        if (version_compare(Kernel::VERSION, '4.1.0', '>=')) {
            $container->registerForAutoconfiguration(SerializerAwareInterface::class)
                ->addMethodCall('setSerializer', [new Reference('tsantos_serializer')]);
        }

        if (null !== $mergedConfig['circular_reference_handler']) {
            $container->getDefinition('tsantos_serializer.object_normalizer')->setArgument(1, new Reference($mergedConfig['circular_reference_handler']));
        }

        if ($debug) {
            $loader->load('debug.xml');
        }

        if (!class_exists(AnnotationReader::class)) {
            $container->removeDefinition('tsantos_serializer.metadata_annotation_driver');
        }

        if (!class_exists(Yaml::class)) {
            $container->removeDefinition('tsantos_serializer.metadata_yaml_driver');
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
            'App\\Entity' => $projectDir.'/src/Entity',
            'App\\Document' => $projectDir.'/src/Document',
            'App\\Model' => $projectDir.'/src/Model',
            '' => $projectDir.'/config/serializer', // should be the last item
        ];

        $pathLocator = function () use ($mappings): ?string {
            foreach ($mappings as $namespace => $path) {
                if (is_dir($path)) {
                    return $namespace;
                }
            }

            return null;
        };

        if (is_dir($configPath = $projectDir.'/config/serializer')) {
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
            $this->createDir($container->getParameterBag()->resolveValue($cacheConfig['path']));
            $container
                ->getDefinition($cacheDefinitionId)
                ->replaceArgument(0, $cacheConfig['path']);
        } elseif (isset($cacheConfig['id'])) {
            $container
                ->getDefinition($cacheDefinitionId)
                ->replaceArgument(0, $cacheConfig['prefix'])
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
