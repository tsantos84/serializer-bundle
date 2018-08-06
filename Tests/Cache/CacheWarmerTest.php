<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests\Cache;

use PHPUnit\Framework\TestCase;
use TSantos\SerializerBundle\Cache\CacheWarmer;
use TSantos\SerializerBundle\ClassLocator;
use TSantos\SerializerBundle\Serializer\Compiler;

class CacheWarmerTest extends TestCase
{
    private $locator;
    private $compiler;
    private $warmer;

    public function setUp()
    {
        $this->locator = $this->createMock(ClassLocator::class);
        $this->compiler = $this->createMock(Compiler::class);
        $this->warmer = new CacheWarmer($this->locator, $this->compiler);
    }

    /** @test */
    public function it_should_compile_the_hydrators()
    {
        $this->locator
            ->expects($this->once())
            ->method('findAllClasses')
            ->willReturn(['My\\Class']);

        $this->compiler
            ->expects($this->once())
            ->method('compile');

        $this->warmer->warmUp('/some/cache/dir');
    }

    /** @test */
    public function it_should_not_compile_the_hydrators()
    {
        $this->locator
            ->expects($this->once())
            ->method('findAllClasses')
            ->will($this->throwException(new \LogicException()));

        $this->compiler
            ->expects($this->never())
            ->method('compile');

        $this->warmer->warmUp('/some/cache/dir');
    }

    /** @test */
    public function it_should_be_optional()
    {
        $this->assertTrue($this->warmer->isOptional());
    }
}
