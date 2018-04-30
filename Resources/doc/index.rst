Installation
============

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

.. code-block:: bash

    $ composer require tsantos/serializer-bundle

Applications that don't use Symfony Flex
----------------------------------------

Step 1: Download the Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

.. code-block:: terminal

    $ composer require tsantos/serializer-bundle

This command requires you to have Composer installed globally, as explained
in the `installation chapter`_ of the Composer documentation.

Step 2: Enable the Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~

Then, enable the bundle by adding it to the list of registered bundles
in the ``app/AppKernel.php`` file of your project:

.. code-block:: php

    <?php
    // app/AppKernel.php

    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...

                new TSantos\SerializerBundle\TSantosSerializerBundle(),
            );

            // ...
        }

        // ...
    }

Step 3: Usage
~~~~~~~~~~~~~

This bundle exposes the service ``tsantos_serializer`` which contains a
reference to :class:``TSantos\Serializer\SerializerInterface``.

.. code-block:: php

    $serializer = $container->get('tsantos_serializer');

.. note::

    The format utilized by the serializer is the one configured in your
    configuration file.

.. _`installation chapter`: https://getcomposer.org/doc/00-intro.md
