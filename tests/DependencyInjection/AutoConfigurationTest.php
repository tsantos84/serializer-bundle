<?php

namespace TSantos\SerializerBundle\Tests\DependencyInjection;

use Symfony\Component\Filesystem\Filesystem;
use TSantos\SerializerBundle\Tests\Fixture\TestKernel;
use TSantos\SerializerBundle\Tests\KernelTestCase;

/**
 * Class AutoConfigurationTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class AutoConfigurationTest extends DependencyInjectionTest
{
    /**
     * @var Filesystem
     */
    private $fs;

    public function setUp()
    {
        parent::setUp();
        $this->fs = new Filesystem();
    }

    /**
     * @test
     * @dataProvider getPaths
     */
    public function it_should_auto_configure_metadata_path_when_the_package_directory_exists(?string $metadataPath, string $namespace)
    {
        $projectDir = $this->projectDir;

        $configMetadataPath = sprintf('%s/config/serializer', $projectDir);
        $metadataPath = sprintf('%s%s', $projectDir, $metadataPath);
        $dirs = [$configMetadataPath, $metadataPath];

        $this->fs->mkdir($dirs);

        $container = $this->getContainer([
            'mapping' => ['auto_configure' => true]
        ]);

        $paths = $container->getDefinition('tsantos_serializer.metadata_file_locator')->getArgument(0);
        $this->assertArrayHasKey($namespace, $paths);
        $this->assertEquals($configMetadataPath, $paths[$namespace]);
    }

    /**
     * @test
     * @dataProvider getSrcPaths
     */
    public function it_should_auto_configure_metadata_path_with_src_directory_only(string $metadataPath, string $namespace)
    {
        $metadataPath = $this->projectDir . $metadataPath;
        $this->fs->mkdir($metadataPath);

        $container = $this->getContainer([
            'mapping' => ['auto_configure' => true]
        ]);

        $paths = $container->getDefinition('tsantos_serializer.metadata_file_locator')->getArgument(0);

        $this->assertArrayHasKey($namespace, $paths);
        $this->assertEquals($metadataPath, $paths[$namespace]);
    }

    public function getPaths()
    {
        return [
            ['/config/serializer', ''],
            ['/src/Document', 'App\Document'],
            ['/src/Model', 'App\Model'],
            ['/src/Entity', 'App\Entity'],
        ];
    }

    public function getSrcPaths()
    {
        return [
            ['/src/Document', 'App\Document'],
            ['/src/Model', 'App\Model'],
            ['/src/Entity', 'App\Entity'],
        ];
    }
}
