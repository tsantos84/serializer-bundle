<?php

/*
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
use TSantos\SerializerBundle\DependencyInjection\Compiler\ConfigureEncoderPass;

/**
 * Class ConfigureEncoderPassTest.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class ConfigureEncoderPassTest extends TestCase
{
    /** @test */
    public function it_should_rewrite_the_definition_of_encoders()
    {
        $container = new ContainerBuilder();

        $container->setParameter('tsantos_serializer.format', 'json');
        $container->register('tsantos_serializer')->setArguments([null, null]);

        $container
            ->register('some_encoder')
            ->setClass('SomeClass')
            ->addTag('tsantos_serializer.encoder', ['format' => 'json']);

        $compiler = new ConfigureEncoderPass();
        $compiler->process($container);

        $definition = $container->getDefinition('tsantos_serializer');
        $encoderReference = $definition->getArgument(0);
        $this->assertSame('some_encoder', (string) $encoderReference);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The tag "tsantos_serializer.encoder" should have the attribute "format"
     */
    public function it_should_not_allow_tag_without_format_attribute()
    {
        $container = new ContainerBuilder();

        $container->setParameter('tsantos_serializer.format', 'json');

        $container
            ->register('some_encoder')
            ->setClass('SomeClass')
            ->addTag('tsantos_serializer.encoder');

        $compiler = new ConfigureEncoderPass();
        $compiler->process($container);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage There is no encoder able to handle "csv" format
     */
    public function it_should_throw_exception_when_the_format_provided_is_not_supported_by_any_encoder()
    {
        $container = new ContainerBuilder();

        $container->setParameter('tsantos_serializer.format', 'csv');

        $container
            ->register('some_encoder')
            ->setClass('SomeClass')
            ->addTag('tsantos_serializer.encoder', ['format' => 'json']);

        $compiler = new ConfigureEncoderPass();
        $compiler->process($container);
    }
}
