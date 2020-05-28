<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Command;

use Metadata\MetadataFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TSantos\Serializer\HydratorCompilerInterface;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\SerializerBundle\Service\ClassNameReader;

/**
 * Class GenerateHydratorCommand.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class GenerateHydratorCommand extends Command
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var ClassNameReader
     */
    private $classReader;

    /**
     * @var HydratorCompilerInterface
     */
    private $compiler;

    /**
     * @var array
     */
    private $directories;

    /**
     * @var array
     */
    private $excluded;

    /**
     * GenerateHydratorCommand constructor.
     */
    public function __construct(MetadataFactoryInterface $metadataFactory, ClassNameReader $classNameReader, HydratorCompilerInterface $compiler, array $directories, array $excluded = [])
    {
        parent::__construct();
        $this->classReader = $classNameReader;
        $this->compiler = $compiler;
        $this->directories = $directories;
        $this->excluded = $excluded;
        $this->metadataFactory = $metadataFactory;
    }

    public function configure()
    {
        $this
            ->setName('serializer:generate-hydrators')
            ->setDescription('Generates the hydrators classes for object (de-)serialization.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->comment('Generating hydrator classes');

        if ($output->isVerbose()) {
            $io->section('Included paths');
            $io->listing($this->directories);
            $io->section('Excluded paths');
            $io->listing(empty($this->excluded) ? ['-'] : $this->excluded);
        }

        try {
            $classes = $this->classReader->readDirectory($this->directories, $this->excluded);
        } catch (\LogicException | \InvalidArgumentException $e) {
            $io->warning('No hydrators to be generated because there is no existing path configured');

            return 0;
        }

        $exitCode = 0;
        $rows = [];

        if ($output->isVerbose()) {
            $io->section('Classes');
        }

        foreach ($classes as $class) {
            try {
                /** @var ClassMetadata $metadata */
                $metadata = $this->metadataFactory->getMetadataForClass($class);
                $this->compiler->compile($metadata);
                $rows[] = [$class, 'OK', '-'];
            } catch (\Throwable $e) {
                $rows[] = [$class, 'NOK', $e->getMessage()];
                $exitCode = 1;
            }
        }

        if ($output->isVerbose()) {
            $io->table(['Class', 'Status', 'Error'], $rows);
        }

        if (0 === $exitCode) {
            $io->success('Hydrator classes generated successfully');
        } else {
            $io->error('Some error occurred while generating the hydrator classes');
        }

        return $exitCode;
    }
}
