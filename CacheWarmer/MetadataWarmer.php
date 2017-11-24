<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Bundle\CacheWarmer;

use Metadata\AdvancedMetadataFactoryInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Class CacheWarmup
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class MetadataWarmer implements CacheWarmerInterface
{
    /**
     * @var AdvancedMetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * CacheWarmer constructor.
     * @param AdvancedMetadataFactoryInterface $metadataFactory
     */
    public function __construct(AdvancedMetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    public function isOptional()
    {
        return true;
    }

    public function warmUp($cacheDir)
    {
        $allClasses = $this->metadataFactory->getAllClassNames();

        foreach ($allClasses as $class) {
            $this->metadataFactory->getMetadataForClass($class);
        }
    }
}
