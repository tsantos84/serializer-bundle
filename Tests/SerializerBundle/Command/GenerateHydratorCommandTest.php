<?php
/**
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests\SerializerBundle\Command;

use Metadata\MetadataFactoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use TSantos\Serializer\HydratorCodeGenerator;
use TSantos\Serializer\HydratorCodeWriter;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\SerializerBundle\Command\GenerateHydratorCommand;
use TSantos\SerializerBundle\Service\ClassReader;

class GenerateHydratorCommandTest extends TestCase
{
    /** @test */
    public function it_should_create_hydrators_properly()
    {
        $tester = $this->createCommandTester();

        $tester->execute(
            [],
            ['decorated' => false]
        );

        $this->assertEquals(0, $tester->getStatusCode(), 'Returns 0 in case of success');
        $this->assertSame('My\DummyClass: OK', trim($tester->getDisplay()));
    }

    /**
     * @return CommandTester
     */
    private function createCommandTester()
    {
        $reader = $this->createMock(ClassReader::class);
        $reader
            ->expects($this->once())
            ->method('read')
            ->willReturn(['My\\DummyClass']);

        $metadataFactory = $this->createMock(MetadataFactoryInterface::class);
        $metadataFactory
            ->expects($this->once())
            ->method('getMetadataForClass')
            ->with('My\\DummyClass')
            ->willReturn($metadata = $this->createMock(ClassMetadata::class));

        $generator = $this->createMock(HydratorCodeGenerator::class);
        $generator
            ->expects($this->once())
            ->method('generate')
            ->with($metadata)
            ->willReturn($code = '<?php class MyHydrator {}');

        $writer = $this->createMock(HydratorCodeWriter::class);
        $writer
            ->expects($this->once())
            ->method('write')
            ->with($metadata, $code);

        $application = new Application();
        $application->add(new GenerateHydratorCommand($reader, $metadataFactory, $generator, $writer));
        return new CommandTester($application->find('serializer:generate_hydrators'));
    }
}
