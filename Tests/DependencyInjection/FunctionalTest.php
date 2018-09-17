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

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use TSantos\Serializer\SerializerInterface;
use TSantos\SerializerBundle\TSantosSerializerBundle;

/**
 * Class FunctionalTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class FunctionalTest extends TestCase
{
    /** @var Kernel */
    private $kernel;

    public function setUp()
    {
        $this->kernel = new Kernel('test', true);
        $this->kernel->boot();
    }

    public function tearDown()
    {
        $this->kernel->shutdown();
    }

    /** @test */
    public function it_can_serialize_an_object_properly()
    {
        $serializer = $this->kernel->getContainer()->get('tsantos_serializer');
        $this->assertInstanceOf(SerializerInterface::class, $serializer);
        $result = $serializer->serialize(new Dummy(1, 'bar'));
        $this->assertSame('{"foo":1,"bar":"bar"}', $result);
    }

    /** @test */
    public function it_can_deserialize_an_object_properly()
    {
        $serializer = $this->kernel->getContainer()->get('tsantos_serializer');
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
            new TSantosSerializerBundle(),
        ];
    }

    public function getRootDir()
    {
        if (null === $this->rootDir) {
            $this->rootDir = __DIR__.'/../../var';
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
     *
     * @param int    $foo
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
