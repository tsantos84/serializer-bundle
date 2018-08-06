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
use TSantos\SerializerBundle\Service\ClassLocator;

/**
 * Class GenerateHydratorCommand.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class GenerateHydratorCommand extends Command
{
    /**
     * @var ClassLocator
     */
    private $classLocator;

    /**
     * @var Compiler
     */
    private $compiler;

    /**
     * GenerateHydratorCommand constructor.
     * @param ClassLocator $classLocator
     * @param Compiler $compiler
     * @param array $directories
     * @param array $excluded
     */
    public function __construct(ClassLocator $classLocator, Compiler $compiler)
    {
        parent::__construct();
        $this->classLocator = $classLocator;
        $this->compiler = $compiler;
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

        $directories = $this->classLocator->getDirectories();
        $excludedDirectories = $this->classLocator->getExcludedDirectories();

        if ($output->isVerbose()) {
            $io->section('Included paths');
            $io->listing($directories);
            $io->section('Excluded paths');
            $io->listing(empty($excludedDirectories) ? ['-'] : $excludedDirectories);
        }

        try {
            $classes = $this->classLocator->findAllClasses();
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
                $this->compiler->compile($class);
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
