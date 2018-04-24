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
        $projectDir = $this->kernel->getProjectDir();

        $this->dirs = [
            sprintf('%s/config', $projectDir),
            sprintf('%s/config/serializer', $projectDir),
            sprintf('%s/src/Document', $projectDir),
            sprintf('%s/src/Entity', $projectDir),
            sprintf('%s/src/Model', $projectDir),
        ];

        $this->fs = new Filesystem();
        $this->fs->mkdir($this->dirs);

        $this->kernel->boot();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->fs->remove($this->dirs);
    }

    /** @test */
    public function it_should_auto_configure_the_metadata_path()
    {
        $container = $this->kernel->getContainer();

        $metadataPaths = $container->getParameter('tsantos_serializer.metadata_paths');

        $this->assertArrayHasKey('App\\', $metadataPaths);
        $this->assertArrayHasKey('App\\Document\\', $metadataPaths);
        $this->assertArrayHasKey('App\\Model\\', $metadataPaths);
        $this->assertArrayHasKey('App\\Entity\\', $metadataPaths);
    }
}
