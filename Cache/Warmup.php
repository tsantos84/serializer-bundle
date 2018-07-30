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

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use TSantos\SerializerBundle\Serializer\Compiler;
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
     * @var Compiler
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
     * GenerateHydratorCommand constructor.
     *
     * @param ClassNameReader $classNameReader
     * @param Compiler        $compiler
     * @param array           $directories
     * @param array           $excluded
     */
    public function __construct(ClassNameReader $classNameReader, Compiler $compiler, array $directories, array $excluded = [])
    {
        $this->classReader = $classNameReader;
        $this->compiler = $compiler;
        $this->directories = $directories;
        $this->excluded = $excluded;
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
            $this->compiler->compile($class);
        }
    }
}
