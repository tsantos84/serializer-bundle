Usage
=====

This bundle exposes the service ``tsantos_serializer`` which contains a
reference to :class:``TSantos\Serializer\SerializerInterface``::

    $serializer = $container->get('tsantos_serializer');
    $serializer = $serializer->serialize($post);
    $serializer = $serializer->deserialize($post, Post::class);

.. note::

    The format used by the serializer is the one configured in your configuration file. Please go to the
    :doc:`configuration_reference` page to see more information about this configuration.

.. note::

    You can create and register new formats. Please, read the dedicated documentation about encoders at :doc:`encoder`
    page.

Auto-wiring the Serializer
~~~~~~~~~~~~~~~~~~~~~~~~~~

Instead of fetching the serializer directly from the container, you can define the serializer as a dependency::

    // src/Controller/DefaultController.php
    use TSantos\Serializer\SerializerInterface;
    use Symfony\Component\HttpFoundation\JsonResponse;

    class DefaultController
    {
        private $serializer;

        public function __construct(SerializerInterface $serializer)
        {
            $this->serializer = $serializer;
        }

        public function indexAction(): JsonResponse
        {
            $post = ...;
            return JsonResponse::fromJsonString($this->serializer->serialize($post));
        }
    }

Sometimes you want to inject the serializer instance by setting it on your service. To easily the injection, you can
implement the SerializerAwareInterface interface and you are done.:

    // src/Controller/DefaultController.php
    use TSantos\Serializer\SerializerInterface;
    use TSantos\Serializer\SerializerAwareInterface;
    use Symfony\Component\HttpFoundation\JsonResponse;

    class DefaultController implements SerializerAwareInterface
    {
        private $serializer;

        public function setSerializer(SerializerInterface $serializer)
        {
            $this->serializer = $serializer;
        }

        public function indexAction(): JsonResponse
        {
            $post = ...;
            return JsonResponse::fromJsonString($this->serializer->serialize($post));
        }
    }

This bundle will automatically call the `setSerializer` method for you. The `TSantos Serializer` library ships with a
useful trait where you can make use instead of add manually the `setSerializer` everywhere you need::

    // src/Controller/DefaultController.php
    use TSantos\Serializer\SerializerInterface;
    use TSantos\Serializer\SerializerAwareInterface;
    use TSantos\Serializer\Traits\SerializerAwareTrait;
    use Symfony\Component\HttpFoundation\JsonResponse;

    class DefaultController implements SerializerAwareInterface
    {
        use SerializerAwareTrait;

        public function indexAction(): JsonResponse
        {
            $post = ...;
            return JsonResponse::fromJsonString($this->serializer->serialize($post));
        }
    }

.. note::

    The ability to auto inject the serializer instance is enable only on version 4.1.0 or above of Symfony Dependency
    Injection.
