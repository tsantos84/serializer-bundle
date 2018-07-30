<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use TSantos\SerializerBundle\Command\GenerateHydratorCommand;
use TSantos\SerializerBundle\Serializer\Compiler;
use TSantos\SerializerBundle\Service\ClassNameReader;

class GenerateHydratorCommandTest extends TestCase
{
    /** @test */
    public function it_should_create_hydrators_properly()
    {
        $tester = $this->createCommandTester();

        $tester->execute(
            [],
            ['decorated' => false, 'verbosity' => OutputInterface::VERBOSITY_VERBOSE]
        );

        $this->assertSame(0, $tester->getStatusCode(), 'Returns 0 in case of success');
        $this->assertSame('My\DummyClass: OK', trim($tester->getDisplay()));
    }

    /**
     * @return CommandTester
     */
    private function createCommandTester()
    {
        $reader = $this->createMock(ClassNameReader::class);
        $reader
            ->expects($this->once())
            ->method('readDirectory')
            ->with(['/some/dir'], ['/some/excluded/dir'])
            ->willReturn(['My\\DummyClass']);

        $compiler = $this->createMock(Compiler::class);
        $compiler
            ->expects($this->once())
            ->method('compile')
            ->with('My\\DummyClass');

        $application = new Application();
        $application->add(new GenerateHydratorCommand($reader, $compiler, ['/some/dir'], ['/some/excluded/dir']));

        return new CommandTester($application->find('serializer:generate_hydrators'));
    }
}
