Console Command
===============

This bundle provides a console command useful to generate hydrator classes.

.. code-block:: bash

    $ bin/console serializer:generate_hydrators

.. note::

    You can see the list of generated classes by adding the `-v` option on the command above.

.. tip::

    If your application is configured to :doc:`never generate </configuration_reference>` hydrator classes, add this
    command to your continuous deploy tool to generate the classes before install your application in production.
