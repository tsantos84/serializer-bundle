<?php

namespace TSantos\SerializerBundle\Cache;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
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
        $generator = $this->createMock(ClassGenerator::class);
        $generator
            ->expects($this->once())
            ->method('generate');

        $app = new Application();
        $app->setAutoExit(false);
        $app->add(new GenerateClassCommand($generator, 'test'));

        $input = new StringInput('serializer:generate-classes');
        $output = new NullOutput();
        $app->run($input, $output);
    }
}
