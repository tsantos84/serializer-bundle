<?php

namespace TSantos\SerializerBundle\Twig;

use TSantos\Serializer\SerializerInterface;

/**
 * Class SerializerExtension
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializerExtension extends \Twig_Extension
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * SerializerExtension constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('serialize', [$this, 'serialize'])
        ];
    }

    public function serialize($data)
    {
        return $this->serializer->serialize($data);
    }
}
