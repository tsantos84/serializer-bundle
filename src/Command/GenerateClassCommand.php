<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\SerializerBundle\Service\ClassGenerator;

/**
 * Class GenerateClassCommand
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class GenerateClassCommand extends Command
{
    /**
     * @var ClassGenerator
     */
    private $classGenerator;

    /**
     * GenerateClassCommand constructor.
     * @param ClassGenerator $classGenerator
     */
    public function __construct(ClassGenerator $classGenerator)
    {
        $this->classGenerator = $classGenerator;
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('serializer:generate-classes')
            ->setDescription('Generates the serializer classes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $this->getApplication()->getKernel()->getEnvironment();
        $style = new SymfonyStyle($input, $output);

        $style->comment(sprintf('Generating <info>%d</info> serializer classes for <info>%s</info> environment',
            count($this->classGenerator),
            $env
        ));

        $this->classGenerator->generate(function (ClassMetadata $metadata) use ($style) {
            $style->writeln(sprintf('<comment>%s</comment>: Ok', $metadata->name), OutputInterface::VERBOSITY_VERBOSE);
        });

        $style->success('Classes for "'.$env.'" environment were successfully generated.');
    }
}
