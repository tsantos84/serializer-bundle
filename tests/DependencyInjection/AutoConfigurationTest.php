<?php

namespace TSantos\SerializerBundle\Tests\DependencyInjection;

use Symfony\Component\Filesystem\Filesystem;
use TSantos\SerializerBundle\Tests\Fixture\TestKernel;
use TSantos\SerializerBundle\Tests\KernelTestCase;

/**
 * Class AutoConfigurationTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 * @group functional
 */
class AutoConfigurationTest extends KernelTestCase
{
    /**
     * @var TestKernel
     */
    private $kernel;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var array
     */
    private $dirs = [];

    protected function setUp()
    {
        $this->kernel = $this->createKernel([
            'mapping' => [
                'auto_configure' => true
            ]
        ], false);

        $this->fs = new Filesystem();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->fs->remove($this->dirs);
    }

    /**
     * @test
     * @dataProvider getPaths
     */
    public function it_should_auto_configure_metadata_path_when_the_package_directory_exists(?string $classPath, string $namespace)
    {
        $projectDir = $this->kernel->getProjectDir();

        $this->dirs = [
            sprintf('%s/config', $projectDir),
            sprintf('%s/config/serializer', $projectDir),
        ];

        if (is_string($classPath)) {
            $this->dirs[] = $projectDir . DIRECTORY_SEPARATOR . $classPath;
        }

        $this->fs->mkdir($this->dirs);
        $this->kernel->boot();
        $container = $this->kernel->getContainer();
        $this->assertArrayHasKey($namespace, $container->getParameter('tsantos_serializer.metadata_paths'));
    }

    /**
     * @test
     * @dataProvider getSrcPaths
     */
    public function it_should_auto_configure_metadata_path_with_src_directory_only(string $classPath, string $namespace)
    {
        $projectDir = $this->kernel->getProjectDir();
        $this->dirs[] = $projectDir . DIRECTORY_SEPARATOR . $classPath;
        $this->fs->mkdir($this->dirs);
        $this->kernel->boot();
        $container = $this->kernel->getContainer();
        $this->assertArrayHasKey($namespace, $container->getParameter('tsantos_serializer.metadata_paths'));
    }

    public function getPaths()
    {
        return [
            [null, ''],
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
