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
use TSantos\SerializerBundle\Service\ClassLocator;

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
// Generating hydrator classes                                                 

Included paths
--------------

 * /some/dir

Excluded paths
--------------

 * /some/excluded/dir

Classes
-------

 --------------- -------- ------- 
  Class           Status   Error  
 --------------- -------- ------- 
  My\DummyClass   OK       -      
 --------------- -------- ------- 

 [OK] Hydrator classes generated successfully
STRING;

        $this->assertSame(0, $tester->getStatusCode(), 'Returns 0 in case of success');
        $this->assertSame($output, trim($tester->getDisplay()));
    }

    /** @test */
    public function it_should_create_hydrators_with_verbose_disabled()
    {
        $tester = $this->createCommandTester($this->returnValue(['My\\DummyClass']));
        $tester->execute([], ['decorated' => false]);
        $output = <<<STRING
// Generating hydrator classes                                                 

 [OK] Hydrator classes generated successfully
STRING;

        $this->assertSame(0, $tester->getStatusCode(), 'Returns 0 in case of success');
        $this->assertSame($output, trim($tester->getDisplay()));
    }

    /** @test */
    public function it_should_display_a_warning_message_for_non_existing_path()
    {
        $tester = $this->createCommandTester($this->throwException(new \LogicException()));
        $tester->execute([], ['decorated' => false]);
        $output = <<<STRING
// Generating hydrator classes                                                 

 [WARNING] No hydrators to be generated because there is no existing path       
           configured
STRING;

        $this->assertSame(0, $tester->getStatusCode(), 'Returns 0 in case of success');
        $this->assertSame($output, trim($tester->getDisplay()));
    }

    /** @test */
    public function it_should_display_error_for_compile_exception_with_verbose_mode_disabled()
    {
        $tester = $this->createCommandTester(
            $this->returnValue(['My\\DummyClass']),
            $this->throwException($ex = new \LogicException('Some exception'))
        );

        $tester->execute([], ['decorated' => false]);

        $output = <<<STRING
// Generating hydrator classes                                                 

 [ERROR] Some error occurred while generating the hydrator classes
STRING;

        $this->assertSame(1, $tester->getStatusCode(), 'Returns 1 in case of exception');
        $this->assertSame($output, trim($tester->getDisplay()));
    }

    /** @test */
    public function it_should_display_error_for_compile_exception_with_verbose_mode_enabled()
    {
        $tester = $this->createCommandTester(
            $this->returnValue(['My\\DummyClass']),
            $this->throwException($ex = new \LogicException('Some exception'))
        );

        $tester->execute([], ['decorated' => false, 'verbosity' => OutputInterface::VERBOSITY_VERBOSE]);

        $output = <<<STRING
// Generating hydrator classes                                                 

Included paths
--------------

 * /some/dir

Excluded paths
--------------

 * /some/excluded/dir

Classes
-------

 --------------- -------- ---------------- 
  Class           Status   Error           
 --------------- -------- ---------------- 
  My\DummyClass   NOK      Some exception  
 --------------- -------- ---------------- 

 [ERROR] Some error occurred while generating the hydrator classes
STRING;

        $this->assertSame(1, $tester->getStatusCode(), 'Returns 1 in case of exception');
        $this->assertSame($output, trim($tester->getDisplay()));
    }

    /**
     * @return CommandTester
     */
    private function createCommandTester($readerBehavior, $compilerBehavior = null)
    {
        $locator = $this->createMock(ClassLocator::class);
        $locator
            ->expects($this->once())
            ->method('findAllClasses')
            ->will($readerBehavior);

        $locator
            ->method('getDirectories')
            ->willReturn(['/some/dir']);

        $locator
            ->method('getExcludedDirectories')
            ->willReturn(['/some/excluded/dir']);

        $compiler = $this->createMock(Compiler::class);
        $compiler
            ->method('compile')
            ->with('My\\DummyClass')
            ->will($compilerBehavior ?? $this->returnSelf());

        $application = new Application();
        $application->add(new GenerateHydratorCommand($locator, $compiler));

        return new CommandTester($application->find('serializer:generate_hydrators'));
    }
}
