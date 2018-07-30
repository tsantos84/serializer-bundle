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

use PHPUnit\Framework\MockObject\Stub;
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
    public function it_should_create_hydrators_with_verbose_enabled()
    {
        $tester = $this->createCommandTester($this->returnValue(['My\\DummyClass']));

        $tester->execute(
            [],
            ['decorated' => false, 'verbosity' => OutputInterface::VERBOSITY_VERBOSE]
        );

        $output = <<<STRING
Included paths
--------------

 * /some/dir

Excluded paths
--------------

 * /some/excluded/dir

My\DummyClass: OK

 [OK] Hydrator classes generated successfully
STRING;

        $this->assertSame(0, $tester->getStatusCode(), 'Returns 0 in case of success');
        $this->assertSame($output, trim($tester->getDisplay()));
    }

    /** @test */
    public function it_should_create_hydrators_with_verbose_disabled()
    {
        $tester = $this->createCommandTester($this->returnValue(['My\\DummyClass']));

        $tester->execute(
            [],
            ['decorated' => false]
        );

        $this->assertSame(0, $tester->getStatusCode(), 'Returns 0 in case of success');
        $this->assertSame('[OK] Hydrator classes generated successfully', trim($tester->getDisplay()));
    }

    /** @test */
    public function it_should_display_a_warning_message_for_non_existing_path()
    {
        $tester = $this->createCommandTester($this->throwException(new \LogicException()));

        $tester->execute(
            [],
            ['decorated' => false]
        );

        $this->assertSame(0, $tester->getStatusCode(), 'Returns 0 in case of success');
        $this->assertSame('[WARNING] No hydrators to be generated because there is no existing path       
           configured', trim($tester->getDisplay()));
    }

    /**
     * @return CommandTester
     */
    private function createCommandTester(Stub $readerBehavior)
    {
        $reader = $this->createMock(ClassNameReader::class);
        $reader
            ->expects($this->once())
            ->method('readDirectory')
            ->with(['/some/dir'], ['/some/excluded/dir'])
            ->will($readerBehavior);

        $compiler = $this->createMock(Compiler::class);
        $compiler
            ->method('compile')
            ->with('My\\DummyClass');

        $application = new Application();
        $application->add(new GenerateHydratorCommand($reader, $compiler, ['/some/dir'], ['/some/excluded/dir']));

        return new CommandTester($application->find('serializer:generate_hydrators'));
    }
}
