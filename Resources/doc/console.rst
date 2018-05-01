Console Command
===============

This bundle provides a console command useful for generate class metadata and serializer classes manually.

.. code-block:: bash

    $ bin/console serializer:generate-classes

.. note::

    You can see the list of generated classes by adding the `-v` option on the command above.

.. tip::

    If your application is configured to never generate serializer classes automatically (best for production), add this
    command to your continuous deploy to generate the classes before install your application in production.

