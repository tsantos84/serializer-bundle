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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TSantos\SerializerBundle\Serializer\Compiler;
use TSantos\SerializerBundle\Service\ClassNameReader;

/**
 * Class GenerateHydratorCommand.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class GenerateHydratorCommand extends Command
{
    /**
     * @var ClassNameReader
     */
    private $classReader;

    /**
     * @var Compiler
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
     *
     * @param ClassNameReader $classNameReader
     * @param Compiler        $compiler
     * @param array           $directories
     * @param array           $excluded
     */
    public function __construct(ClassNameReader $classNameReader, Compiler $compiler, array $directories, array $excluded = [])
    {
        parent::__construct();
        $this->classReader = $classNameReader;
        $this->compiler = $compiler;
        $this->directories = $directories;
        $this->excluded = $excluded;
    }

    public function configure()
    {
        $this
            ->setName('serializer:generate_hydrators')
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
            $io->listing($this->excluded);
        }

        try {
            $classes = $this->classReader->readDirectory($this->directories, $this->excluded);
        } catch (\LogicException | \InvalidArgumentException $e) {
            $io->warning('No hydrators to be generated because there is no existing path configured');

            return 0;
        }

        $exitCode = 0;

        foreach ($classes as $class) {
            $io->write($class.': ', false, OutputInterface::VERBOSITY_VERBOSE);
            try {
                $this->compiler->compile($class);
                $io->writeln('OK', OutputInterface::VERBOSITY_VERBOSE);
            } catch (\Throwable $e) {
                $io->writeln('NOK - ' . $e->getMessage(), OutputInterface::VERBOSITY_VERBOSE);
                $exitCode = 1;
            }
        }

        if (0 === $exitCode) {
            $io->success('Hydrator classes generated successfully');
        } else {
            $io->error('Some error occurred while generating the hydrator classes');
        }

        return $exitCode;
    }
}
