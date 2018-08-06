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
use TSantos\SerializerBundle\ClassLocator;
use TSantos\SerializerBundle\Serializer\Compiler;

/**
 * Class CacheWarmer.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class CacheWarmer implements CacheWarmerInterface
{
    /**
     * @var ClassLocator
     */
    private $classLocator;

    /**
     * @var Compiler
     */
    private $compiler;

    /**
     * Warmup constructor.
     *
     * @param ClassLocator $classLocator
     * @param Compiler     $compiler
     */
    public function __construct(ClassLocator $classLocator, Compiler $compiler)
    {
        $this->classLocator = $classLocator;
        $this->compiler = $compiler;
    }

    public function isOptional()
    {
        return true;
    }

    public function warmUp($cacheDir)
    {
        try {
            $classes = $this->classLocator->findAllClasses();
        } catch (\LogicException | \InvalidArgumentException $e) {
            return;
        }

        foreach ($classes as $class) {
            $this->compiler->compile($class);
        }
    }
}
