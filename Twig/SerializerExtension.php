<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Twig;

use TSantos\Serializer\SerializerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class SerializerExtension.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class SerializerExtension extends AbstractExtension
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * SerializerExtension constructor.
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('serialize', [$this, 'serialize'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function serialize($data)
    {
        return $this->serializer->serialize($data);
    }
}
