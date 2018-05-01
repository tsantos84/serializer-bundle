Twig
====

This bundle ships with a simple Twig Filter that allows you serialize objects directly from twig template:

.. code:: html

    {# templates/index/posts.json.twig #}
    {{ posts|serialize }}
