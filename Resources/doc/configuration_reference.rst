Configuration Reference
=======================

Default Configuration
---------------------

.. code-block:: yaml

    tsantos_serializer:
        debug: "%kernel.debug%"
        format: "json"
        hydrator_path: "%kernel.cache_dir%/tsantos_serializer/classes"
        generation_strategy: "file_not_exists"
        circular_reference_handler: "tsantos_serializer.default_circular_reference_handler"
        mapping:
            auto_configure: true
            paths: {  }
            include_dir: ["%kernel.project_dir%/src/{Entity,Document,Model,ValueObject}"]
            exclude_dir: ["%kernel.project_dir%/src/{Entity,Document,Model,ValueObject}"]
            cache:
                type: file
                prefix: TSantosSerializer
                path: "%kernel.cache_dir%/tsantos_serializer/metadata"

Configuration Reference
-----------------------

    debug
        Enable or disable some production optimizations.

    format
        The input and output format of serialization operations (e.g: encoder)

    hydrator_path
        Directory that will store the hydrator classes

    generation_strategy
        Class generation strategy. Can be one of:

        `never`: serializer classes will never be generated (best for production environment,
        but will require to generate the classes in your continous deployment system).

        `always`: every time a new class will be generated (best for debugging).

        `file_not_found`: serializer classes will be generated only if the class not exists
        (best for development environment)

    circular_reference_handler:
        Service ID of the service that will handle Circular Reference Exceptions. Pass `~` to this option
        to disable handling and force exceptions to be throw.

    mapping.auto_configure
        The bundle will try to read the mappings automatically from the directories following the order bellow:

        1. `config/serializer` - YAML or XML configuration files
        2. `src/Entity` - Annotations
        3. `src/Document` - Annotations
        4. `src/Model` - Annotations

    mapping.paths
        A list of paths and its namespaces like so:

        .. code:: yaml

            tsantos_serializer:
                mapping:
                    paths:
                        - { namespace: "My\\Document", path: "%kernel.project_dir%/config/serializer" }

    mapping.include_dir:
        A list of directories where serializer will look for classes to generate its corresponding hydrators

    mapping.exclude_dir:
        A list of directories that serializer should exclude when looking for classes to generate its corresponding hydrators

    mapping.cache.type
        Type of cache implementation. Should be one of `file`, `psr` or `doctrine`.

    mapping.cache.prefix
        Prefix of cache keys. Required only for `doctrine` and `psr` cache types.

    mapping.cache.path
        Directory that will stored the metadata cache files. Required only for `file` cache type.
