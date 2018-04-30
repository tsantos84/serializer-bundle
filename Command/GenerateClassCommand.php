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
     * @var string
     */
    private $environment;

    /**
     * GenerateClassCommand constructor.
     * @param ClassGenerator $classGenerator
     * @param string $environment
     */
    public function __construct(ClassGenerator $classGenerator, string $environment)
    {
        parent::__construct();
        $this->classGenerator = $classGenerator;
        $this->environment = $environment;
    }

    public function configure()
    {
        $this
            ->setName('serializer:generate-classes')
            ->setDescription('Generates the serializer classes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);

        $style->comment(sprintf('Generating <info>%d</info> serializer classes for <info>%s</info> environment',
            count($this->classGenerator),
            $this->environment
        ));

        $this->classGenerator->generate(function (ClassMetadata $metadata) use ($style) {
            $style->writeln(sprintf('<comment>%s</comment>: Ok', $metadata->name), OutputInterface::VERBOSITY_VERBOSE);
        });

        $style->success('Classes for "'.$this->environment.'" environment were successfully generated.');
    }
}
