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
use Symfony\Component\Console\Tester\CommandTester;
use TSantos\Serializer\HydratorCompiler;
use TSantos\SerializerBundle\Command\EnsureProductionSettingsCommand;

class EnsureProductionSettingsCommandTest extends TestCase
{
    /** @test */
    public function it_should_throws_error_if_debug_mode_is_enabled()
    {
        $tester = $this->createCommandTester(true, HydratorCompiler::AUTOGENERATE_NEVER);

        $tester->execute([]);

        $output = <<<STRING
[ERROR] Debug mode should be disabled on production. You should set            
         `tsantos_serializer.debug` to false to fix this problem.
STRING;

        $this->assertSame(1, $tester->getStatusCode(), 'Returns 1 in case of error');
        $this->assertSame($output, trim($tester->getDisplay()));
    }

    /** @test */
    public function it_should_throws_error_if_generation_strategy_is_not_configured_to_never()
    {
        $tester = $this->createCommandTester(false, HydratorCompiler::AUTOGENERATE_ALWAYS);

        $tester->execute([]);

        $output = <<<STRING
[ERROR] Serializer is not configured to never generate hydrators on production.
         You should set the option `tsantos_serializer.generation_strategy` to  
         "never" to fix this problem.
STRING;

        $this->assertSame(1, $tester->getStatusCode(), 'Returns 1 in case of error');
        $this->assertSame($output, trim($tester->getDisplay()));
    }

    /** @test */
    public function it_should_not_throws_error_for_properly_configuration()
    {
        $tester = $this->createCommandTester(false, HydratorCompiler::AUTOGENERATE_NEVER);

        $tester->execute([]);

        $output = <<<STRING
[OK] Serializer settings is configured for production properly
STRING;

        $this->assertSame(0, $tester->getStatusCode(), 'Returns 0 in case of success');
        $this->assertSame($output, trim($tester->getDisplay()));
    }

    /**
     * @return CommandTester
     */
    private function createCommandTester(bool $debug, int $strategy)
    {
        $application = new Application();
        $application->add(new EnsureProductionSettingsCommand($debug, $strategy));

        return new CommandTester($application->find('serializer:ensure-production-settings'));
    }
}
