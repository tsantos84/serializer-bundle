<?php

namespace TSantos\SerializerBundle\Tests;

use PHPUnit\Framework\TestCase;
use TSantos\Serializer\SerializerClassLoader;
use TSantos\Serializer\SerializerInterface;

/**
 * Class FunctionalTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class FunctionalTest extends TestCase
{
    public function tearDown()
    {
        $command = 'rm -rf ' . __DIR__ . '/var';
        exec($command);
    }

    public function testParametersWiring()
    {
        $kernel = $this->createKernel([
            'debug' => false,
            'class_path' => '%kernel.cache_dir%/tsantos_serializer/classes',
            'generate_strategy' => SerializerClassLoader::AUTOGENERATE_ALWAYS,
            'mapping' => [
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
    }

    public function testServicesWiring()
    {
        $kernel = $this->createKernel([
            'debug' => false,
            'class_path' => '%kernel.cache_dir%/tsantos_serializer/classes',
            'generate_strategy' => SerializerClassLoader::AUTOGENERATE_ALWAYS,
            'mapping' => [
                'paths' => [
                    ['path' => __DIR__, 'namespace' => 'My\Namespace']
                ]
            ]
        ]);

        $container = $kernel->getContainer();

        $this->assertInstanceOf(SerializerInterface::class, $container->get('tsantos_serializer'));
    }

    public function testClassPathDirectoryCreation()
    {
        $kernel = $this->createKernel([
            'class_path' => '%kernel.cache_dir%/tsantos_serializer/classes',
            'mapping' => [
                'paths' => [
                    ['path' => __DIR__]
                ]
            ]
        ]);

        $this->assertDirectoryExists($kernel->getCacheDir() .  '/tsantos_serializer');
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testRequiringOneMetadataPathAtLeast()
    {
        $this->createKernel([
            'mapping' => ['paths' => []]
        ]);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid configuration for path "tsantos_serializer.mapping.paths.0.path": The path ""\/some\/invalid\/path"" does not exit
     */
    public function testProperMetadataPath()
    {
        $this->createKernel([
            'mapping' => [
                'paths' => [
                    ['path' => '/some/invalid/path']
                ]
            ]
        ]);
    }

    public function testFileCacheConfiguration()
    {
        $kernel = $this->createKernel([
            'mapping' => [
                'cache' => ['path' => '%kernel.cache_dir%/tsantos_serializer/metadata'],
                'paths' => [
                    ['path' => __DIR__]
                ]
            ]
        ]);

        $this->assertDirectoryExists($kernel->getCacheDir() . '/tsantos_serializer/metadata');
    }

    private function createKernel(array $config = [])
    {
        $kernel = new TestKernel($config);
        $kernel->boot();
        return $kernel;
    }
}
