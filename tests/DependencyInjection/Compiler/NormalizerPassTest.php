<?php
/**
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSantos\SerializerBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TSantos\SerializerBundle\DependencyInjection\Compiler\NormalizerPass;

/**
 * Class NormalizerPassTest
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class NormalizerPassTest extends TestCase
{
    /** @test */
    public function it_should_add_method_calls_to_normalizer_registry_definition()
    {
        $container = new ContainerBuilder();
        $container->register('tsantos_serializer.normalizer_registry');

        $container
            ->register('some_normalizer')
            ->addTag('tsantos_serializer.normalizer')
            ->addTag('tsantos_serializer.denormalizer');

        $compiler = new NormalizerPass();
        $compiler->process($container);

        $definition = $container->getDefinition('tsantos_serializer.normalizer_registry');
        $this->assertCount(1, $definition->getMethodCalls());
    }
}
