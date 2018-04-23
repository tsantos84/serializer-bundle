<?php

namespace TSantos\SerializerBundle\Tests;

use PHPUnit\Framework\TestCase;
use TSantos\SerializerBundle\Tests\Fixture\TestKernel;

/**
 * Class KernelTestCase
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
abstract class KernelTestCase extends TestCase
{
    public function tearDown()
    {
        $command = 'rm -rf ' . __DIR__ . '/var';
        exec($command);
    }

    protected function createKernel(array $config = [])
    {
        $kernel = new TestKernel($config);
        $kernel->boot();
        return $kernel;
    }
}
