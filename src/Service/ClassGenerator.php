<?php

namespace TSantos\SerializerBundle\Service;

use Metadata\AdvancedMetadataFactoryInterface;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\SerializerClassCodeGenerator;
use TSantos\Serializer\SerializerClassWriter;

/**
 * Class ClassGenerator
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ClassGenerator implements \Countable
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
     * ClassGenerator constructor.
     * @param AdvancedMetadataFactoryInterface $metadataFactory
     * @param SerializerClassCodeGenerator $codeGenerator
     * @param SerializerClassWriter $writer
     */
    public function __construct(AdvancedMetadataFactoryInterface $metadataFactory, SerializerClassCodeGenerator $codeGenerator, SerializerClassWriter $writer)
    {
        $this->metadataFactory = $metadataFactory;
        $this->codeGenerator = $codeGenerator;
        $this->writer = $writer;
    }

    public function generate(callable $success = null)
    {
        $allClasses = $this->metadataFactory->getAllClassNames();

        foreach ($allClasses as $class) {
            /** @var ClassMetadata $metadata */
            $metadata = $this->metadataFactory->getMetadataForClass($class);
            $code = $this->codeGenerator->generate($metadata);
            $this->writer->write($metadata, $code);
            if (is_callable($success)) {
                call_user_func($success, $metadata);
            }
        }
    }

    public function count(): int
    {
        return count($this->metadataFactory->getAllClassNames());
    }
}
