<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\DataCollector;

use Metadata\Driver\AdvancedDriverInterface;
use Metadata\MetadataFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use TSantos\Serializer\Metadata\ClassMetadata;
use TSantos\Serializer\Metadata\PropertyMetadata;
use TSantos\SerializerBundle\ClassLocator;

/**
 * Class SerializerCollector.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializerCollector extends DataCollector
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var AdvancedDriverInterface[]
     */
    private $advancedDrivers;

    /**
     * @var ClassLocator
     */
    private $classLocator;

    /**
     * SerializerCollector constructor.
     *
     * @param MetadataFactoryInterface $metadataFactory
     * @param array                            $advancedDrivers
     * @param ClassLocator                     $classLocator
     */
    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        array $advancedDrivers,
        ClassLocator $classLocator
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->advancedDrivers = $advancedDrivers;
        $this->classLocator = $classLocator;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = [
            'mapped_classes' => [],
            'auto_mapped_classes' => [],
        ];

        $mappedClasses = $this->doGetMappedClasses();
        $autoMappedClasses = $this->doGetAutoMappedClasses(array_keys($mappedClasses));

        $this->data['mapped_classes'] = $this->createMappingInfo($mappedClasses);
        $this->data['auto_mapped_classes'] = $this->createMappingInfo($autoMappedClasses);
    }

    public function getMappedClasses(): array
    {
        return $this->data['mapped_classes'];
    }

    public function getAutoMappedClasses(): array
    {
        return $this->data['auto_mapped_classes'];
    }

    public function getName()
    {
        return 'serializer';
    }

    public function reset()
    {
        $this->data = [];
    }

    /**
     * @return ClassMetadata[]
     */
    private function doGetMappedClasses(): array
    {
        $mapped = [];

        $classes = array_map(function (AdvancedDriverInterface $driver) {
            return $driver->getAllClassNames();
        }, $this->advancedDrivers);

        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($classes));

        foreach ($iterator as $className) {
            $mapped[$className] = $this->metadataFactory->getMetadataForClass($className);
        }

        return $mapped;
    }

    private function doGetAutoMappedClasses(array $excluded): array
    {
        try {
            $classes = $this->classLocator->findAllClasses();
        } catch (\LogicException | \InvalidArgumentException $e) {
            return [];
        }

        $classes = array_diff($classes, $excluded);

        return array_map(function (string $class) {
            return $this->metadataFactory->getMetadataForClass($class);
        }, $classes);
    }

    /**
     * @param ClassMetadata[] $metadataClasses
     *
     * @return array
     */
    private function createMappingInfo(array $classes): array
    {
        $info = [];
        foreach ($classes as $metadata) {
            $mapping = array_map(function (PropertyMetadata $propertyMetadata): array {
                return [
                    'name' => $propertyMetadata->name,
                    'exposeAs' => $propertyMetadata->exposeAs,
                    'type' => $propertyMetadata->type,
                    'options' => $propertyMetadata->options,
                ];
            }, $metadata->propertyMetadata);

            $info[] = [
                'name' => $metadata->name,
                'filename' => $metadata->reflection->getFileName(),
                'path' => $metadata->reflection->getFilename(),
                'line' => $metadata->reflection->getStartLine(),
                'mapping' => $this->cloneVar($mapping),
            ];
        }

        return $info;
    }
}
