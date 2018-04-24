<?php

namespace TSantos\SerializerBundle\Tests\DependencyInjection;

use TSantos\Serializer\SerializerClassLoader;
use TSantos\Serializer\SerializerInterface;
use TSantos\SerializerBundle\Tests\KernelTestCase;

/**
 * Class FunctionalTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @group functional
 */
class ConfigurationTest extends KernelTestCase
{
    /** @test */
    public function it_should_save_the_parameters_in_the_container_properly()
    {
        $kernel = $this->createKernel([
            'debug' => false,
            'class_path' => '%kernel.cache_dir%/tsantos_serializer/classes',
            'generate_strategy' => 'always',
            'format' => 'json',
            'mapping' => [
                'auto_configure' => false,
                'cache' => [
                    'prefix' => 'cache_prefix'
                ],
                'paths' => [
                    ['path' => __DIR__, 'namespace' => 'My\Namespace']
                ]
            ]
        ]);

        $container = $kernel->getContainer();

        $this->assertFalse($container->getParameter('tsantos_serializer.debug'));
        $this->assertEquals($kernel->getCacheDir() . '/tsantos_serializer/classes', $container->getParameter('tsantos_serializer.class_path'));
        $this->assertEquals(SerializerClassLoader::AUTOGENERATE_ALWAYS, $container->getParameter('tsantos_serializer.class_generate_strategy'));
        $this->assertEquals(['My\Namespace' => __DIR__], $container->getParameter('tsantos_serializer.metadata_paths'));
        $this->assertEquals('cache_prefix', $container->getParameter('tsantos_serializer.metadata_cache_prefix'));
        $this->assertEquals('json', $container->getParameter('tsantos_serializer.format'));
    }

    /** @test */
    public function it_should_register_the_services_in_the_container_properly()
    {
        $kernel = $this->createKernel([
            'debug' => false,
            'class_path' => '%kernel.cache_dir%/tsantos_serializer/classes',
            'generate_strategy' => 'always',
            'mapping' => [
                'auto_configure' => false,
                'paths' => [
                    ['path' => __DIR__, 'namespace' => 'My\Namespace']
                ]
            ]
        ]);

        $container = $kernel->getContainer();

        $this->assertInstanceOf(SerializerInterface::class, $container->get('tsantos_serializer'));
    }

    /** @test */
    public function it_should_create_the_directory_to_store_the_generated_classes()
    {
        $kernel = $this->createKernel([
            'class_path' => '%kernel.cache_dir%/tsantos_serializer/classes',
            'mapping' => [
                'auto_configure' => false,
                'paths' => [
                    ['path' => __DIR__]
                ]
            ]
        ]);

        $this->assertDirectoryExists($kernel->getCacheDir() .  '/tsantos_serializer');
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid configuration for path "tsantos_serializer.mapping.paths.0.path": The path ""\/some\/invalid\/path"" does not exit
     */
    public function it_should_not_allow_a_non_existing_directory_for_metadata()
    {
        $this->createKernel([
            'mapping' => [
                'auto_configure' => false,
                'paths' => [
                    ['path' => '/some/invalid/path']
                ]
            ]
        ]);
    }

    /** @test */
    public function it_should_create_the_directory_to_store_the_metadata_cache()
    {
        $kernel = $this->createKernel([
            'mapping' => [
                'auto_configure' => false,
                'cache' => ['path' => '%kernel.cache_dir%/tsantos_serializer/metadata'],
                'paths' => [
                    ['path' => __DIR__]
                ]
            ]
        ]);

        $this->assertDirectoryExists($kernel->getCacheDir() . '/tsantos_serializer/metadata');
    }
}
