<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Serializer;

use Metadata\MetadataFactoryInterface;
use TSantos\Serializer\HydratorCodeGenerator;
use TSantos\Serializer\HydratorCodeWriter;
use TSantos\Serializer\Metadata\ClassMetadata;

/**
 * Class Compiler.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Compiler
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var HydratorCodeGenerator
     */
    private $generator;

    /**
     * @var HydratorCodeWriter
     */
    private $writer;

    /**
     * Compiler constructor.
     *
     * @param MetadataFactoryInterface $metadataFactory
     * @param HydratorCodeGenerator    $generator
     * @param HydratorCodeWriter       $writer
     */
    public function __construct(MetadataFactoryInterface $metadataFactory, HydratorCodeGenerator $generator, HydratorCodeWriter $writer)
    {
        $this->metadataFactory = $metadataFactory;
        $this->generator = $generator;
        $this->writer = $writer;
    }

    public function compile(string $class): void
    {
        /** @var ClassMetadata $metadata */
        $metadata = $this->metadataFactory->getMetadataForClass($class);
        $code = $this->generator->generate($metadata);
        $this->writer->write($metadata, $code);
    }
}
