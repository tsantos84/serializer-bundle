<?php
/**
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Filesystem\Filesystem;
use TSantos\Serializer\SerializerInterface;
use TSantos\SerializerBundle\TSantosSerializerBundle;

/**
 * Class FunctionalTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class FunctionalTest extends TestCase
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var Filesystem
     */
    private $fs;

    public function setUp()
    {
        $this->kernel = new Kernel('test', true);
        $this->fs = new Filesystem();
    }

    public function tearDown()
    {
        exec(sprintf('rm -rf %s', $this->kernel->getProjectDir()));
    }

    /** @test */
    public function it_should_register_the_serializer_service()
    {
        $this->kernel->boot();
        $container = $this->kernel->getContainer();

        $this->assertTrue($container->has('tsantos_serializer'));
        $this->assertInstanceOf(SerializerInterface::class, $container->get('tsantos_serializer'));
    }

    /** @test @dataProvider getSrcPaths */
    public function it_should_configure_serializer_with_config_and_src_paths(string $srcPath)
    {
        $projectDir = $this->kernel->getProjectDir();

        // write the config content
        $this->fs->mkdir(sprintf('%s/config/serializer', $projectDir));
        $configContent = file_get_contents(__DIR__ . '/Resources/config/serializer/Post.xml');
        $configContent = str_replace('_NAMESPACE_', $srcPath, $configContent);
        file_put_contents(sprintf('%s/config/serializer/Post.xml', $projectDir), $configContent);

        // write the class content
        $this->fs->mkdir(sprintf('%s/src/%s', $projectDir, $srcPath));
        $classContent = file_get_contents(__DIR__ . '/Fixture/Post.php');
        $classContent = str_replace('_NAMESPACE_', $srcPath, $classContent);
        file_put_contents($filename = sprintf('%s/src/%s/Post.php', $projectDir, $srcPath), $classContent);

        $this->kernel->boot();
        require_file($filename);

        $serializer = $this->kernel->getContainer()->get('tsantos_serializer');

        $class = 'App\\' . $srcPath . '\\Post';
        $post = new $class(1, 'Data Transformation', 'Serializer helps to...');
        $expected = '{"id":1,"title":"Data Transformation","summary":"Serializer helps to..."}';
        $this->assertEquals($expected, $serializer->serialize($post));
    }

    public function getSrcPaths()
    {
        return [
            ['Entity'],
            ['Document'],
            ['Model'],
        ];
    }
}

class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    public function getProjectDir()
    {
        return __DIR__ . '/../project-tmp';
    }

    public function getCacheDir()
    {
        return $this->getProjectDir() . '/var/cache';
    }

    public function getLogDir()
    {
        return $this->getProjectDir() . '/var/logs';
    }

    public function registerBundles()
    {
        return [
            new TSantosSerializerBundle()
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
//        $loader->load(function(ContainerBuilder $container) {
//            $container->loadFromExtension('tsantos_serializer', [
//                'mapping' => []
//            ]);
//        });
    }
}

function require_file($filename) {
    require_once($filename);
}
