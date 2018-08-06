<?php
/**
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Service;

/**
 * Class ClassLocator
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ClassLocator
{
    /**
     * @var ClassNameReader
     */
    private $classNameReader;

    /**
     * @var array
     */
    private $directories;

    /**
     * @var array
     */
    private $excluded;

    /**
     * ClassLocator constructor.
     * @param ClassNameReader $classNameReader
     * @param array $directories
     * @param array $excluded
     */
    public function __construct(ClassNameReader $classNameReader, array $directories, array $excluded = [])
    {
        $this->classNameReader = $classNameReader;
        $this->directories = $directories;
        $this->excluded = $excluded;
    }

    public function findAllClasses(): array
    {
        return $this->classNameReader->readDirectory($this->directories, $this->excluded);
    }

    /**
     * @return array
     */
    public function getDirectories(): array
    {
        return $this->directories;
    }

    /**
     * @return array
     */
    public function getExcludedDirectories(): array
    {
        return $this->excluded;
    }
}
