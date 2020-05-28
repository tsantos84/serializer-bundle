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

use Symfony\Component\Filesystem\Filesystem;

/**
 * Class AutoConfigurationTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class AutoConfigurationTest extends DependencyInjectionTest
{
    /**
     * @var Filesystem
     */
    private $fs;

    public function setUp(): void
    {
        parent::setUp();
        $this->fs = new Filesystem();
    }

    /**
     * @test
     * @dataProvider getPaths
     */
    public function it_should_auto_configure_metadata_path_when_the_package_directory_exists(?string $srcMetadata, string $namespace)
    {
        $projectDir = $this->projectDir;

        $configMetadataPath = sprintf('%s/config/serializer', $projectDir);
        $srcMetadata = sprintf('%s%s', $projectDir, $srcMetadata);
        $dirs = [$configMetadataPath, $srcMetadata];

        $this->fs->mkdir($dirs);

        $container = $this->getContainer([
            'mapping' => ['auto_configure' => true],
        ]);

        $paths = $container->getDefinition('tsantos_serializer.metadata_file_locator')->getArgument(0);

        $this->assertArrayHasKey($namespace, $paths);
        $this->assertSame($configMetadataPath, $paths[$namespace]);
    }

    /**
     * @test
     * @dataProvider getSrcPaths
     */
    public function it_should_auto_configure_metadata_path_with_src_directory_only(string $metadataPath, string $namespace)
    {
        $metadataPath = $this->projectDir.$metadataPath;
        $this->fs->mkdir($metadataPath);

        $container = $this->getContainer([
            'mapping' => ['auto_configure' => true],
        ]);

        $paths = $container->getDefinition('tsantos_serializer.metadata_file_locator')->getArgument(0);

        $this->assertArrayHasKey($namespace, $paths);
        $this->assertSame($metadataPath, $paths[$namespace]);
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
