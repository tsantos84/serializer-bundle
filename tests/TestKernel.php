<?php

namespace TSantos\SerializerBundle\Tests;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TSantos\SerializerBundle\TSantosSerializerBundle;

/**
 * Class TestKernel
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class TestKernel extends \Symfony\Component\HttpKernel\Kernel
{
    private $serializerConfig = [];

    public function __construct(array $config = [])
    {
        $this->serializerConfig = $config;
        parent::__construct('test', true);
    }

    public function getCacheDir()
    {
        return $this->getProjectDir().'/tests/var/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return $this->getProjectDir().'/tests/var/log';
    }

    public function registerBundles()
    {
        return [
            new TSantosSerializerBundle()
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function(ContainerBuilder $container) {
            $container->register('event_dispatcher', EventDispatcher::class);
            $container->loadFromExtension('tsantos_serializer', $this->serializerConfig);
        });
    }
}
