<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Cache;

use Metadata\MetadataFactoryInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use TSantos\Serializer\HydratorCompilerInterface;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\SerializerBundle\Service\ClassNameReader;

/**
 * Class Warmup.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Warmup implements CacheWarmerInterface
{
    /**
     * @var ClassNameReader
     */
    private $classReader;

    /**
     * @var HydratorCompilerInterface
     */
    private $compiler;

    /**
     * @var array
     */
    private $directories;

    /**
     * @var array
     */
    private $excluded;
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * GenerateHydratorCommand constructor.
     */
    public function __construct(ClassNameReader $classNameReader, HydratorCompilerInterface $compiler, MetadataFactoryInterface $metadataFactory, array $directories, array $excluded = [])
    {
        $this->classReader = $classNameReader;
        $this->compiler = $compiler;
        $this->directories = $directories;
        $this->excluded = $excluded;
        $this->metadataFactory = $metadataFactory;
    }

    public function isOptional()
    {
        return true;
    }

    public function warmUp($cacheDir)
    {
        try {
            $classes = $this->classReader->readDirectory($this->directories, $this->excluded);
        } catch (\LogicException | \InvalidArgumentException $e) {
            return;
        }

        foreach ($classes as $class) {
            /** @var ClassMetadata $metadata */
            $metadata = $this->metadataFactory->getMetadataForClass($class);
            $this->compiler->compile($metadata);
        }
    }
}
