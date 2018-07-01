<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Service;

use Metadata\AdvancedMetadataFactoryInterface;
use TSantos\Serializer\HydratorCodeGenerator;
use TSantos\Serializer\HydratorCodeWriter;
use TSantos\Serializer\Metadata\ClassMetadata;

/**
 * Class ClassGenerator.
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
     * @var HydratorCodeGenerator
     */
    private $codeGenerator;

    /**
     * @var HydratorCodeWriter
     */
    private $writer;

    /**
     * ClassGenerator constructor.
     *
     * @param AdvancedMetadataFactoryInterface $metadataFactory
     * @param HydratorCodeGenerator            $codeGenerator
     * @param HydratorCodeWriter               $writer
     */
    public function __construct(AdvancedMetadataFactoryInterface $metadataFactory, HydratorCodeGenerator $codeGenerator, HydratorCodeWriter $writer)
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
