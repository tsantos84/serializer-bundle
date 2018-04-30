Event Dispatcher
================

Sometimes you need to change the state of the data before and/or after some serialization operation. In TSantos Serializer
you can accomplish this through `Event Listeners` and `Event Subscribers`.

Event Listeners
---------------

Event listeners are single PHP methods or `callable` that will be called when some event is triggered by the serializer.
To register listener in this bundle, first you need to create a class::

    <?php

        namespace App\EventListener;

        use TSantos\Serializer\EventDispatcher\Event\PreSerializationEvent;
        use TSantos\Serializer\EventDispatcher\EventSubscriberInterface;
        use TSantos\Serializer\EventDispatcher\SerializerEvents;

        class PostSerializerListener
        {
            public function onPreSerialization(PreSerializationEvent $event): void
            {
                $post = $event->getData();
                $post->setTitle('serialized_title');
            }
        }

.. code:: yaml

Then register and tag it with `tsantos_serializer.event_listener`::

    services:
        App\EventListener\PostSerializerListener:
            tags:
                - { name: "tsantos_serializer.event_listener", event:"serializer.pre_serialization", method:"onPreSerialization" }

You can even omit the attribute `method` from the tag if your class have the `__invoke` method::

    <?php

        namespace App\EventListener;

        use TSantos\Serializer\EventDispatcher\Event\PreSerializationEvent;
        use TSantos\Serializer\EventDispatcher\EventSubscriberInterface;
        use TSantos\Serializer\EventDispatcher\SerializerEvents;

        class PostSerializerListener
        {
            public function __invoke(PreSerializationEvent $event): void
            {
                $post = $event->getData();
                $post->setTitle('serialized_title');
            }
        }

.. code:: yaml

   services:
        App\EventListener\PostSerializerListener:
            tags:
                - { name: "tsantos_serializer.event_listener", event:"serializer.pre_serialization" }


Event Subscribers
-----------------

A better way to define event listener is through event subscribers. All the above examples can be achieved by creating
a subscriber class::

    <?php

    namespace App\EventListener;

    use TSantos\Serializer\EventDispatcher\Event\PreSerializationEvent;
    use TSantos\Serializer\EventDispatcher\EventSubscriberInterface;
    use TSantos\Serializer\EventDispatcher\SerializerEvents;

    class PostSerializerListener implements EventDispatcherInterface
    {
        public static function getListeners(): array
        {
            return [
                SerializerEvents::PRE_SERIALIZATION => 'onPreSerialization',
            ];
        }

        public function onPreSerialization(PreSerializationEvent $event): void
        {
            $post = $event->getData();
            $post->setTitle('serialized_title');
        }
    }

Thanks to Symfony DIC`s auto-configuration mechanism, all you need to do is to create your subscriber class and
make sure that it implements the :class:`EventDispatcher\\EventDispatcherInterface` interface.

.. note::

    If you are using a Symfony version prior to 3.3, you'll need to register and tag manually the service.

    .. code:: yaml

        services:
            App\EventListener\PostSerializerListener:
                tags:
                    - { name: "tsantos_serializer.event_subscriber"}
