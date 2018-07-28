<?php
/**
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Command;

use Metadata\MetadataFactory;
use Metadata\MetadataFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TSantos\Serializer\HydratorCodeGenerator;
use TSantos\Serializer\HydratorCodeWriter;
use TSantos\SerializerBundle\Service\ClassReader;

/**
 * Class GenerateHydratorCommand
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class GenerateHydratorCommand extends Command
{
    /**
     * @var ClassReader
     */
    private $classReader;

    /**
     * @var HydratorCodeGenerator
     */
    private $generator;

    /**
     * @var HydratorCodeWriter
     */
    private $writer;

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * GenerateHydratorCommand constructor.
     * @param ClassReader $classReader
     * @param MetadataFactory $metadataFactory
     * @param HydratorCodeGenerator $generator
     * @param HydratorCodeWriter $writer
     */
    public function __construct(ClassReader $classReader, MetadataFactoryInterface $metadataFactory, HydratorCodeGenerator $generator, HydratorCodeWriter $writer)
    {
        parent::__construct();
        $this->classReader = $classReader;
        $this->generator = $generator;
        $this->writer = $writer;
        $this->metadataFactory = $metadataFactory;
    }

    public function configure()
    {
        $this
            ->setName('serializer:generate_hydrators')
            ->setDescription('Generates the hydrators classes for object (de-)serialization.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $classes = $this->classReader->read();
        $io = new SymfonyStyle($input, $output);

        foreach ($classes as $class) {
            $io->write($class . ': ');
            $metadata = $this->metadataFactory->getMetadataForClass($class);
            $code = $this->generator->generate($metadata);
            $this->writer->write($metadata, $code);
            $io->write('OK');
        }
    }
}
