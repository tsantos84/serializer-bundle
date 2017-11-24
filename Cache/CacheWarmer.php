<?php
/**
 * This file is part of the TSantos Serializer package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\Bundle\Cache;

use Metadata\AdvancedMetadataFactoryInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use TSantos\Serializer\SerializerClassCodeGenerator;
use TSantos\Serializer\SerializerClassWriter;

/**
 * Class CacheWarmer
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class CacheWarmer implements CacheWarmerInterface
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
     * CacheWarmer constructor.
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

    public function isOptional()
    {
        return true;
    }

    public function warmUp($cacheDir)
    {
        $allClasses = $this->metadataFactory->getAllClassNames();

        foreach ($allClasses as $class) {
            $metadata = $this->metadataFactory->getMetadataForClass($class);
            $code = $this->codeGenerator->generate($metadata);
            $this->writer->write($metadata, $code);
        }
    }
}
