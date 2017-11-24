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

use Metadata\AdvancedMetadataFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TSantos\Serializer\SerializerClassCodeGenerator;
use TSantos\Serializer\SerializerClassWriter;

/**
 * Class GenerateClassCommand
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class GenerateClassCommand extends ContainerAwareCommand
{
    /**
     * @var AdvancedMetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var SerializerClassCodeGenerator
     */
    private $codeGenerator;

    /**
     * @var SerializerClassWriter
     */
    private $writer;

    /**
     * GenerateClassCommand constructor.
     * @param AdvancedMetadataFactoryInterface $metadataFactory
     * @param SerializerClassCodeGenerator $codeGenerator
     * @param SerializerClassWriter $writer
     */
    public function __construct(AdvancedMetadataFactoryInterface $metadataFactory, SerializerClassCodeGenerator $codeGenerator, SerializerClassWriter $writer)
    {
        $this->metadataFactory = $metadataFactory;
        $this->codeGenerator = $codeGenerator;
        $this->writer = $writer;
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
