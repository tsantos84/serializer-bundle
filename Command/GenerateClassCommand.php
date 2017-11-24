<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class GenerateClassCommand
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class GenerateClassCommand extends ContainerAwareCommand
{
    private $metadataFactory;
    private $codeGenerator;
    private $writer;

    public function configure()
    {
        $this
            ->setName('serializer:generate-classes')
            ->setDescription('Generates the serializer classes');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $this->metadataFactory = $container->get('tsantos_serializer.metadata_factory');
        $this->codeGenerator = $container->get('tsantos_serializer.class_code_generator');
        $this->writer = $container->get('tsantos_serializer.class_writer');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $this->getApplication()->getKernel()->getEnvironment();
        $allClasses = $this->metadataFactory->getAllClassNames();
        $style = new SymfonyStyle($input, $output);

        $style->comment(sprintf('Generating <info>%d</info> serializer classes for <info>%s</info> environment',
            count($allClasses),
            $env
        ));

        foreach ($allClasses as $class) {
            $metadata = $this->metadataFactory->getMetadataForClass($class);
            $code = $this->codeGenerator->generate($metadata);
            $this->writer->write($metadata, $code);
            $style->writeln(sprintf('<comment>%s</comment>: Ok', $class), OutputInterface::VERBOSITY_VERBOSE);
        }

        $style->success('Classes for "'.$env.'" environment were successfully generated.');
    }
}
