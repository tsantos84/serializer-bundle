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
use TSantos\Serializer\HydratorCompiler;

/**
 * Class EnsureProductionSettingsCommand.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class EnsureProductionSettingsCommand extends Command
{
    /**
     * @var bool
     */
    private $debug;

    /**
     * @var int
     */
    private $strategy;

    /**
     * @var
     */
    private $caching;

    /**
     * EnsureProductionSettingsCommand constructor.
     */
    public function __construct(bool $debug, int $strategy)
    {
        parent::__construct();
        $this->debug = $debug;
        $this->strategy = $strategy;
    }

    public function configure()
    {
        $this
            ->setName('serializer:ensure-production-settings')
            ->setDescription('Ensures that serializer is proper configured for production');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->debug) {
            $io->error('Debug mode should be disabled on production. You should set `tsantos_serializer.debug` to false to fix this problem.');

            return 1;
        }

        if (HydratorCompiler::AUTOGENERATE_NEVER !== $this->strategy) {
            $io->error('Serializer is not configured to never generate hydrators on production. You should set the option `tsantos_serializer.generation_strategy` to "never" to fix this problem.');

            return 1;
        }

        $io->success('Serializer settings is configured for production properly');

        return 0;
    }
}
