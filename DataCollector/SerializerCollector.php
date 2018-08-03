<?php
/**
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\DataCollector;

use Metadata\AdvancedMetadataFactoryInterface;
use Metadata\Driver\DriverChain;
use Metadata\Driver\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use TSantos\Serializer\Metadata\PropertyMetadata;

/**
 * Class SerializerCollector
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializerCollector extends DataCollector
{
    /**
     * @var AdvancedMetadataFactoryInterface
     */
    private $metadataFactory;
    /**
     * @var FileLocator
     */
    private $fileLocator;

    /**
     * SerializerCollector constructor.
     * @param AdvancedMetadataFactoryInterface $metadataFactory
     * @param FileLocator $fileLocator
     */
    public function __construct(AdvancedMetadataFactoryInterface $metadataFactory, FileLocator $fileLocator)
    {
        $this->metadataFactory = $metadataFactory;
        $this->fileLocator = $fileLocator;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = [
            'mapped_classes' => []
        ];

        foreach (['App\\Entity\\Post'] as $className) {

            $metadata = $this->metadataFactory->getMetadataForClass($className);

            $mapping = array_map(function (PropertyMetadata $propertyMetadata): array {
                return [
                    'name' => $propertyMetadata->name,
                    'exposeAs' => $propertyMetadata->exposeAs,
                    'type' => $propertyMetadata->type,
                    'options' => $propertyMetadata->options,
                ];
            }, $metadata->propertyMetadata);

            $this->data['mapped_classes'][] = [
                'name' => $className,
                'path' => '/config/serializer/Post.yaml',
                'mapping' => $this->cloneVar($mapping)
            ];
        }
    }

    public function getMappedClasses(): array
    {
        return $this->data['mapped_classes'];
    }

    public function getName()
    {
        return 'serializer';
    }

    public function reset()
    {
        $this->data = [];
    }
}
