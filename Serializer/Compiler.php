<?php
/**
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Serializer;
use Metadata\MetadataFactory;
use TSantos\Serializer\HydratorCodeGenerator;
use TSantos\Serializer\HydratorCodeWriter;
use TSantos\Serializer\Metadata\ClassMetadata;

/**
 * Class Compiler
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Compiler
{
    /**
     * @var HydratorCodeGenerator
     */
    private $generator;

    /**
     * @var HydratorCodeWriter
     */
    private $writer;

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * Compiler constructor.
     * @param HydratorCodeGenerator $generator
     * @param HydratorCodeWriter $writer
     * @param MetadataFactory $metadataFactory
     */
    public function __construct(HydratorCodeGenerator $generator, HydratorCodeWriter $writer, MetadataFactory $metadataFactory)
    {
        $this->generator = $generator;
        $this->writer = $writer;
        $this->metadataFactory = $metadataFactory;
    }

    public function compile(string $class): void
    {
        /** @var ClassMetadata $metadata */
        $metadata = $this->metadataFactory->getMetadataForClass($class);
        $code = $this->generator->generate($metadata);
        $this->writer->write($metadata, $code);
    }
}
