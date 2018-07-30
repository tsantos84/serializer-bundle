<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use TSantos\SerializerBundle\DependencyInjection\TSantosSerializerExtension;

/**
 * Class DependencyInjectionTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
abstract class DependencyInjectionTest extends TestCase
{
    protected $projectDir;
    protected $cacheDir;

    public function setUp()
    {
        $this->projectDir = realpath(__DIR__.'/../../').'/project-tmp';
        $this->cacheDir = $this->projectDir.'/var/cache/test';
    }

    public function tearDown()
    {
        if (is_dir($this->projectDir)) {
            $command = 'rm -rf '.$this->projectDir;
            exec($command);
        }
    }

    protected function getContainer(array $config = []): ContainerBuilder
    {
        $extension = new TSantosSerializerExtension();
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.debug' => true,
            'kernel.cache_dir' => $this->cacheDir,
            'kernel.project_dir' => $this->projectDir,
            'kernel.environment' => 'test',
        ]));

        // disable auto-configuration on unit testing
        $defaults = ['mapping' => ['auto_configure' => false]];

        $extension->load([$defaults, $config], $container);

        return $container;
    }

    protected function assertDICHasParameter(ContainerBuilder $container, string $name, $value = null)
    {
        if (2 === func_num_args()) {
            $this->assertArrayHasKey($name, $container->getParameterBag()->all(), 'Expected container has parameter '.$name);
        } else {
            $this->assertSame($value, $container->getParameter($name), 'Expected container has parameter '.$name.' with value '.is_scalar($value) ? $value : gettype($value));
        }
    }

    protected function assertDICDefinitionHasArgument(Definition $definition, $argument, $value)
    {
        $this->assertSame($value, $definition->getArgument($argument), sprintf('Expected the argument#%s of definition "%s" have the value "%s"', $argument, $definition->getClass(), is_scalar($value) ? $value : gettype($value)));
    }

    /**
     * Method copied from MonologBundle.
     *
     * @see https://github.com/symfony/monolog-bundle/blob/master/Tests/DependencyInjection/DependencyInjectionTest.php#L34
     */
    protected function assertDICDefinitionMethodCallAt(Definition $definition, int $pos, string $methodName, array $params = null)
    {
        $calls = $definition->getMethodCalls();
        if (isset($calls[$pos][0])) {
            $this->assertSame($methodName, $calls[$pos][0], "Method '".$methodName."' is expected to be called at position $pos.");

            if (null !== $params) {
                $this->assertSame($params, $calls[$pos][1], "Expected parameters to methods '".$methodName."' do not match the actual parameters.");
            }
        } else {
            $this->fail("Method '".$methodName."' is expected to be called at position $pos.");
        }
    }

    protected function getDICDefinitionMethodArgsAt(Definition $definition, int $pos)
    {
        $calls = $definition->getMethodCalls();

        if (0 === count($calls)) {
            $this->fail('No method call registered for definition '.$definition->getClass());
        }

        return $calls[$pos][1];
    }
}
