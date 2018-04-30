Usage
=====

This bundle exposes the service ``tsantos_serializer`` which contains a
reference to :class:``TSantos\Serializer\SerializerInterface``.

.. code-block:: php

    $serializer = $container->get('tsantos_serializer');

.. note::

    The format used by the serializer is the one configured in your configuration file. Please go to the
    :doc:`configuration_reference` page to see more information about this configuration.

.. note::

    You can create and register new formats. Please, read the dedicated documentation about encoders at :doc:`encoder`
    page.

Auto-wiring the Serializer
~~~~~~~~~~~~~~~~~~~~~~~~~~

Instead of fetching the serializer directly from the container, you can define the serializer as a dependency.

.. code-block:: php

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
