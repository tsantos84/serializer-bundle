<?php

namespace TSantos\SerializerBundle\Cache;

use PHPUnit\Framework\TestCase;
use TSantos\SerializerBundle\Command\GenerateClassCommand;
use TSantos\SerializerBundle\Service\ClassGenerator;

/**
 * Class GenerateClassCommandTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class GenerateClassCommandTest extends TestCase
{
    /** @test */
    public function it_should_generate_the_classes_properly()
    {
        $this->markTestIncomplete('Need to add the Symfony Console Component to test this class');
        $generator = $this->createMock(ClassGenerator::class);
        $command = new GenerateClassCommand($generator);

        $this->warmer->warmUp('/some/path');
    }
}
