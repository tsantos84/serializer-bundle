<?php

namespace TSantos\SerializerBundle\Tests\DependencyInjection;

use Metadata\Driver\DriverInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use TSantos\Serializer\EventDispatcher\EventSubscriberInterface;
use TSantos\Serializer\Normalizer\DenormalizerInterface;
use TSantos\Serializer\Normalizer\NormalizerInterface;
use TSantos\Serializer\SerializerClassLoader;

/**
 * Class TSantosExtensionTest
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
        $this->assertDICHasParameter($container,'tsantos_serializer.format', 'json');
    }

    /** @test */
    public function it_can_register_parameters_with_custom_values_properly()
    {
        $container = $this->getContainer([
            'debug' => false,
            'format' => 'xml'
        ]);
        $this->assertDICHasParameter($container, 'tsantos_serializer.debug', false);
        $this->assertDICHasParameter($container,'tsantos_serializer.format', 'xml');
    }

    /** @test */
    public function it_should_setup_the_class_path_with_default_value_properly()
    {
        $container = $this->getContainer();

        $this->assertDICDefinitionHasArgument(
            $container->getDefinition('tsantos_serializer.class_writer'),
            0,
            $dir = '%kernel.cache_dir%/tsantos_serializer/classes'
        );

        $path = $container->getParameterBag()->resolveValue($dir);

        $this->assertDirectoryExists($path);
    }

    /** @test */
    public function it_should_setup_the_class_path_with_custom_value_properly()
    {
        $container = $this->getContainer(['class_path' => '%kernel.cache_dir%/tsantos_serializer/classes_custom']);

        $this->assertDICDefinitionHasArgument(
            $container->getDefinition('tsantos_serializer.class_writer'),
            0,
            $dir = '%kernel.cache_dir%/tsantos_serializer/classes_custom'
        );

        $path = $container->getParameterBag()->resolveValue($dir);

        $this->assertDirectoryExists($path);
    }

    /** @test @dataProvider getClassLoaderStrategy */
    public function it_should_configure_the_class_loader_service_properly(string $name, int $expected)
    {
        $container = $this->getContainer([
            'generate_strategy' => $name
        ]);

        $this->assertDICDefinitionHasArgument($container->getDefinition('tsantos_serializer.class_loader'), 3, $expected);
    }

    public function getClassLoaderStrategy()
    {
        return [
            ['never', SerializerClassLoader::AUTOGENERATE_NEVER],
            ['always', SerializerClassLoader::AUTOGENERATE_ALWAYS],
            ['file_not_exists', SerializerClassLoader::AUTOGENERATE_FILE_NOT_EXISTS],
        ];
    }

    /** @test */
    public function it_should_configure_custom_metadata_paths_provided_in_configuration_properly()
    {
        $container = $this->getContainer([
            'mapping' => [
                'paths' => [
                    [
                        'namespace' => $namespace = 'App\Entity',
                        'path' => $path = __DIR__ . '/../Fixture'
                    ]
                ]
            ]
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
                    ['path' => '/some/invalid/path']
                ]
            ]
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
                    'path' => $dir = $this->cacheDir . '/custom_path'
                ]
            ]
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
                    'prefix' => 'my_prefix_'
                ]
            ]
        ]);

        $this->assertDICDefinitionHasArgument($container->getDefinition('tsantos_serializer.metadata_psr_cache'), 0, 'my_prefix_');
        $this->assertDICDefinitionHasArgument($container->getDefinition('tsantos_serializer.metadata_psr_cache'), 1, 'some_psr_service');
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
                    'prefix' => 'my_prefix_'
                ]
            ]
        ]);

        $this->assertDICDefinitionHasArgument($container->getDefinition('tsantos_serializer.metadata_doctrine_cache'), 0, 'my_prefix_');
        $this->assertDICDefinitionHasArgument($container->getDefinition('tsantos_serializer.metadata_doctrine_cache'), 1, 'some_doctrine_service');
        $this->assertMetadataFactoryCache($container, 'tsantos_serializer.metadata_doctrine_cache');
    }

    /** @test @dataProvider getInterfacesForAutoConfiguration */
    public function it_should_register_services_for_auto_configuration(string $interface, string $tag)
    {
        $container = $this->getContainer();
        $instances = $container->getAutoconfiguredInstanceof();
        $this->assertArrayHasKey($interface, $instances);

        /** @var ChildDefinition $definition */
        $definition = $instances[$interface];

        $this->assertTrue($definition->hasTag($tag));
    }

    public function getInterfacesForAutoConfiguration()
    {
        return [
            [DriverInterface::class, 'tsantos_serializer.metadata_driver'],
            [NormalizerInterface::class, 'tsantos_serializer.normalizer'],
            [DenormalizerInterface::class, 'tsantos_serializer.denormalizer'],
            [EventSubscriberInterface::class, 'tsantos_serializer.event_subscriber'],
        ];
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
            'debug' => false
        ]);
        $this->assertFalse($container->hasDefinition('tsantos_serializer.stopwatch_listener'));
    }

    private function assertMetadataFactoryCache(ContainerBuilder $container, string $expectedService)
    {
        $factoryDefinition = $container->getDefinition('tsantos_serializer.metadata_factory');
        $this->assertDICDefinitionMethodCallAt($factoryDefinition, 0,'setCache');

        /** @var Reference $reference */
        $args = $this->getDICDefinitionMethodArgsAt($factoryDefinition, 0);
        $reference = $args[0];
        $this->assertEquals($expectedService, $reference);
    }
}
