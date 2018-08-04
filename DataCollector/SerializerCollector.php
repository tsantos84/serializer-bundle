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
use Metadata\Driver\AdvancedDriverInterface;
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
     * @var AdvancedDriverInterface[]
     */
    private $advancedDrivers;

    /**
     * SerializerCollector constructor.
     * @param AdvancedMetadataFactoryInterface $metadataFactory
     * @param AdvancedDriverInterface[] $advancedDrivers
     */
    public function __construct(AdvancedMetadataFactoryInterface $metadataFactory, array $advancedDrivers)
    {
        $this->metadataFactory = $metadataFactory;
        $this->advancedDrivers = $advancedDrivers;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = [
            'mapped_classes' => []
        ];

        $classes = array_map(function (AdvancedDriverInterface $driver) {
            return $driver->getAllClassNames();
        }, $this->advancedDrivers);

        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($classes));

        foreach ($iterator as $className) {

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
                'filename' => $metadata->reflection->getFileName(),
                'line' => $metadata->reflection->getStartLine(),
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
