<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Cache;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use TSantos\SerializerBundle\Service\ClassGenerator;

/**
 * Class CacheWarmer
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class CacheWarmer implements CacheWarmerInterface
{
    /**
     * @var ClassGenerator
     */
    private $generator;

    /**
     * CacheWarmer constructor.
     * @param ClassGenerator $classGenerator
     */
    public function __construct(ClassGenerator $classGenerator)
    {
        $this->generator = $classGenerator;
    }

    public function isOptional()
    {
        return true;
    }

    public function warmUp($cacheDir)
    {
        $this->generator->generate();
    }
}
