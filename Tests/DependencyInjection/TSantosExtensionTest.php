<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests\DependencyInjection;

use Metadata\Driver\DriverInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\OutOfBoundsException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use TSantos\Serializer\EventDispatcher\EventSubscriberInterface;
use TSantos\Serializer\HydratorLoader;
use TSantos\Serializer\Metadata\ConfiguratorInterface;
use TSantos\Serializer\Normalizer\DenormalizerInterface;
use TSantos\Serializer\Normalizer\NormalizerInterface;
use TSantos\Serializer\SerializerAwareInterface;

/**
 * Class TSantosExtensionTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class TSantosExtensionTest extends DependencyInjectionTest
{
    /** @test */
    public function it_can_register_parameters_with_default_values_properly()
    {
        $container = $this->getContainer();
        $this->assertDICHasParameter($container, 'tsantos_serializer.debug', true);
        $this->assertDICHasParameter($container, 'tsantos_serializer.format', 'json');
        $this->assertDICHasParameter($container, 'tsantos_serializer.include_dir', [
            '%kernel.project_dir%/src/{Entity,Document,Model,ValueObject}',
        ]);
        $this->assertDICHasParameter($container, 'tsantos_serializer.exclude_dir', []);
    }

    /** @test */
    public function it_can_register_parameters_with_custom_values_properly()
    {
        $container = $this->getContainer([
            'debug' => false,
            'format' => 'xml',
            'mapping' => [
                'include_dir' => '/some/path',
                'exclude_dir' => '/some/excluded/path'
            ]
        ]);
        $this->assertDICHasParameter($container, 'tsantos_serializer.debug', false);
        $this->assertDICHasParameter($container, 'tsantos_serializer.format', 'xml');
        $this->assertDICHasParameter($container, 'tsantos_serializer.include_dir', ['/some/path']);
        $this->assertDICHasParameter($container, 'tsantos_serializer.exclude_dir', ['/some/excluded/path']);
    }

    /** @test */
    public function it_should_setup_the_hydrator_path_with_default_value_properly()
    {
        $container = $this->getContainer();

        $this->assertDICDefinitionHasArgument(
            $container->getDefinition('tsantos_serializer.hydrator_code_writer'),
            0,
            $dir = '%kernel.cache_dir%/tsantos_serializer/hydrators'
        );

        $path = $container->getParameterBag()->resolveValue($dir);

        $this->assertDirectoryExists($path);
    }

    /** @test */
    public function it_should_setup_the_hydrator_path_with_custom_value_properly()
    {
        $container = $this->getContainer(['hydrator_path' => '%kernel.cache_dir%/tsantos_serializer/hydrators_custom']);

        $this->assertDICDefinitionHasArgument(
            $container->getDefinition('tsantos_serializer.hydrator_code_writer'),
            0,
            $dir = '%kernel.cache_dir%/tsantos_serializer/hydrators_custom'
        );

        $path = $container->getParameterBag()->resolveValue($dir);

        $this->assertDirectoryExists($path);
    }

    /** @test @dataProvider getClassLoaderStrategy */
    public function it_should_configure_the_hydrator_loader_service_properly(string $name, int $expected)
    {
        $container = $this->getContainer([
            'generation_strategy' => $name,
        ]);

        $this->assertDICDefinitionHasArgument($container->getDefinition('tsantos_serializer.hydrator_loader'), 3, $expected);
    }

    public function getClassLoaderStrategy()
    {
        return [
            ['never', HydratorLoader::AUTOGENERATE_NEVER],
            ['always', HydratorLoader::AUTOGENERATE_ALWAYS],
            ['file_not_exists', HydratorLoader::AUTOGENERATE_FILE_NOT_EXISTS],
        ];
    }

    /** @test */
    public function it_should_configure_custom_metadata_paths_provided_in_configuration_properly()
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir($path = $this->projectDir.'/src/Model');

        $container = $this->getContainer([
            'mapping' => [
                'paths' => [
                    [
                        'namespace' => $namespace = 'App\Entity',
                        'path' => $path,
                    ],
                ],
            ],
        ]);

        $expected = [$namespace => $path];

        $this->assertDICDefinitionHasArgument($container->getDefinition('tsantos_serializer.metadata_file_locator'), 0, $expected);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid configuration for path "tsantos_serializer.mapping.paths.0.path": The path ""\/some\/invalid\/path"" does not exit
     */
    public function it_should_not_allow_configure_non_existing_metadata_path()
    {
        $this->getContainer([
            'mapping' => [
                'paths' => [
                    ['path' => '/some/invalid/path'],
                ],
            ],
        ]);
    }

    /** @test */
    public function it_should_configure_file_cache_with_default_values_properly()
    {
        $container = $this->getContainer();
        $dir = '%kernel.cache_dir%/tsantos_serializer/metadata';
        $this->assertDICDefinitionHasArgument($container->getDefinition('tsantos_serializer.metadata_file_cache'), 0, $dir);
        $dir = $container->getParameterBag()->resolveValue($dir);
        $this->assertDirectoryExists($dir);
    }

    /** @test */
    public function it_should_configure_file_cache_with_custom_values_properly()
    {
        $container = $this->getContainer([
            'mapping' => [
                'cache' => [
                    'path' => $dir = $this->cacheDir.'/custom_path',
                ],
            ],
        ]);

        $this->assertDICDefinitionHasArgument($container->getDefinition('tsantos_serializer.metadata_file_cache'), 0, $dir);
        $this->assertDirectoryExists($dir);
        $this->assertMetadataFactoryCache($container, 'tsantos_serializer.metadata_file_cache');
    }

    /** @test */
    public function it_should_configure_psr_cache_properly()
    {
        $container = $this->getContainer([
            'mapping' => [
                'cache' => [
                    'type' => 'psr',
                    'id' => 'some_psr_service',
                    'prefix' => 'my_prefix_',
                ],
            ],
        ]);

        $this->assertDICDefinitionHasArgument($container->getDefinition('tsantos_serializer.metadata_psr_cache'), 0, 'my_prefix_');
        $this->assertSame(
            (string) $container
                ->getDefinition('tsantos_serializer.metadata_psr_cache')
                ->getArgument(1),
            'some_psr_service'
        );
        $this->assertMetadataFactoryCache($container, 'tsantos_serializer.metadata_psr_cache');
    }

    /** @test */
    public function it_should_configure_doctrine_cache_properly()
    {
        $container = $this->getContainer([
            'mapping' => [
                'cache' => [
                    'type' => 'doctrine',
                    'id' => 'some_doctrine_service',
                    'prefix' => 'my_prefix_',
                ],
            ],
        ]);

        $this->assertDICDefinitionHasArgument($container->getDefinition('tsantos_serializer.metadata_doctrine_cache'), 0, 'my_prefix_');
        $this->assertSame(
            (string) $container
                ->getDefinition('tsantos_serializer.metadata_doctrine_cache')
                ->getArgument(1),
            'some_doctrine_service'
        );
        $this->assertMetadataFactoryCache($container, 'tsantos_serializer.metadata_doctrine_cache');
    }

    /**
     * @test
     * @dataProvider getCacheType
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage You need to configure the node "mapping.cache.id"
     */
    public function it_should_require_the_service_id_for_cache_type_psr_or_doctrine(string $cacheType)
    {
        $this->getContainer([
            'mapping' => [
                'cache' => [
                    'type' => $cacheType,
                    'id' => null,
                    'prefix' => 'my_prefix_',
                ],
            ],
        ]);
    }

    public function getCacheType()
    {
        return [
            ['doctrine'],
            ['psr'],
        ];
    }

    /** @test @dataProvider getInterfacesWithTheirTagsForAutoConfiguration */
    public function it_should_add_tags_for_services_with_auto_configuration(string $interface, string $tag)
    {
        $container = $this->getContainer();
        $instances = $container->getAutoconfiguredInstanceof();
        $this->assertArrayHasKey($interface, $instances);

        /** @var ChildDefinition $definition */
        $definition = $instances[$interface];

        $this->assertTrue($definition->hasTag($tag));
    }

    public function getInterfacesWithTheirTagsForAutoConfiguration()
    {
        return [
            [DriverInterface::class, 'tsantos_serializer.metadata_driver'],
            [NormalizerInterface::class, 'tsantos_serializer.normalizer'],
            [DenormalizerInterface::class, 'tsantos_serializer.denormalizer'],
            [EventSubscriberInterface::class, 'tsantos_serializer.event_subscriber'],
            [ConfiguratorInterface::class, 'tsantos_serializer.metadata_configurator'],
        ];
    }

    /** @test */
    public function it_should_inject_the_serializer_in_classes_implementing_SerializerAwareInterface()
    {
        if (!version_compare(Kernel::VERSION, '4.1.0', '>=')) {
            $this->markTestSkipped(
                'Auto injection of serializer instance is not enabled prior version 4.1.0 of Symfony Http '.
                'Kernel package. Current version is '.Kernel::VERSION
            );
        }

        $container = $this->getContainer();
        $instances = $container->getAutoconfiguredInstanceof();
        $this->assertArrayHasKey(SerializerAwareInterface::class, $instances);

        /** @var ChildDefinition $definition */
        $definition = $instances[SerializerAwareInterface::class];

        $this->assertDICDefinitionMethodCallAt($definition, 0, 'setSerializer');
    }

    /** @test */
    public function it_should_load_debug_services()
    {
        $container = $this->getContainer();
        $this->assertTrue($container->hasDefinition('tsantos_serializer.stopwatch_listener'));
    }

    /** @test */
    public function it_should_not_load_debug_services_if_debug_mode_is_off()
    {
        $container = $this->getContainer([
            'debug' => false,
        ]);
        $this->assertFalse($container->hasDefinition('tsantos_serializer.stopwatch_listener'));
    }

    /** @test */
    public function it_should_inject_the_default_circular_reference_handler_into_object_normalizer()
    {
        $container = $this->getContainer();

        $this->assertSame(
            'tsantos_serializer.default_circular_reference_handler',
            (string) $container->getDefinition('tsantos_serializer.object_normalizer')->getArgument(2)
        );
    }

    /** @test */
    public function it_should_inject_a_custom_circular_reference_handler_into_object_normalizer()
    {
        $container = $this->getContainer([
            'circular_reference_handler' => 'my_handler',
        ]);

        $this->assertSame(
            'my_handler',
            (string) $container->getDefinition('tsantos_serializer.object_normalizer')->getArgument(2)
        );
    }

    /** @test */
    public function it_should_not_inject_any_circular_reference_handler_into_object_normalizer()
    {
        $this->expectException(OutOfBoundsException::class);

        $container = $this->getContainer([
            'circular_reference_handler' => null,
        ]);

        $container->getDefinition('tsantos_serializer.object_normalizer')->getArgument(2);
    }

    private function assertMetadataFactoryCache(ContainerBuilder $container, string $expectedService)
    {
        $factoryDefinition = $container->getDefinition('tsantos_serializer.metadata_factory');
        $this->assertDICDefinitionMethodCallAt($factoryDefinition, 0, 'setCache');

        /** @var Reference $reference */
        $args = $this->getDICDefinitionMethodArgsAt($factoryDefinition, 0);
        $reference = $args[0];
        $this->assertSame($expectedService, (string) $reference);
    }
}
