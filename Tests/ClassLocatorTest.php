<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests;

use PHPUnit\Framework\TestCase;
use TSantos\SerializerBundle\ClassLocator;
use TSantos\SerializerBundle\ClassNameReader;

class ClassLocatorTest extends TestCase
{
    /** @test */
    public function it_can_find_all_classes()
    {
        $reader = $this->createMock(ClassNameReader::class);
        $reader
            ->expects($this->once())
            ->method('readDirectory')
            ->with(['/some/path'], ['/some/excluded/path'])
            ->willReturn(['My\\Class']);

        $locator = new ClassLocator($reader, ['/some/path'], ['/some/excluded/path']);

        $this->assertSame(['My\\Class'], $locator->findAllClasses());
        $this->assertSame(['/some/path'], $locator->getDirectories());
        $this->assertSame(['/some/excluded/path'], $locator->getExcludedDirectories());
    }
}
