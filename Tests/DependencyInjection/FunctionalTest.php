<?php

namespace TSantos\SerializerBundle\Tests\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TSantos\Serializer\SerializerInterface;
use TSantos\SerializerBundle\TSantosSerializerBundle;

/**
 * Class FunctionalTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class FunctionalTest extends KernelTestCase
{
    public function setUp()
    {
        $_SERVER['KERNEL_CLASS'] = Kernel::class;
    }

    /** @test */
    public function it_can_serialize_an_object_properly()
    {
        $kernel = static::bootKernel();
        $serializer = $kernel->getContainer()->get('tsantos_serializer');
        $this->assertInstanceOf(SerializerInterface::class, $serializer);
        $result = $serializer->serialize(new Dummy(1, 'bar'));
        $this->assertSame('{"foo":1,"bar":"bar"}', $result);
    }

    /** @test */
    public function it_can_deserialize_an_object_properly()
    {
        $kernel = static::bootKernel();
        $serializer = $kernel->getContainer()->get('tsantos_serializer');
        $this->assertInstanceOf(SerializerInterface::class, $serializer);
        $result = $serializer->deserialize('{"foo":1,"bar":"bar"}', Dummy::class);
        $this->assertInstanceOf(Dummy::class, $result);
    }
}

class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    public function registerBundles()
    {
        return [
            new TSantosSerializerBundle()
        ];
    }

    public function getRootDir()
    {
        if (null === $this->rootDir) {
            $this->rootDir = __DIR__ . '/../../var';
        }

        return $this->rootDir;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }
}

class Dummy
{
    /** @var int */
    private $foo;

    /** @var string */
    private $bar;

    /**
     * Dummy constructor.
     * @param int $foo
     * @param string $bar
     */
    public function __construct(int $foo, string $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    /**
     * @return int
     */
    public function getFoo(): int
    {
        return $this->foo;
    }

    /**
     * @param int $foo
     */
    public function setFoo(int $foo): void
    {
        $this->foo = $foo;
    }
}
