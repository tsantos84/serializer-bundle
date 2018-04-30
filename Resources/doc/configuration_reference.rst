Configuration Reference
=======================

.. code-block:: yaml

    tsantos_serializer:
        debug: "%kernel.debug%"

        # Format of serialization operations (e.g: encoder)
        format: "json"

        # Directory that will store the generated classes
        class_path: "%kernel.cache_dir%/tsantos_serializer/classes"

        # Class generation strategy. Can be one of:
        #
        # `never`: means that the serializer classes will never be generated (best for production environment,
        # but will require to generate the classes in your continous deployment system).
        #
        # `always`: means that every time a new class will be generated (best for debugging).
        #
        # `file_not_found`: means that the serializer classes will be generated only if the class not exists
        # (best for development environment)
        generate_strategy: "file_not_exists"

        mapping:
            # The bundle will try to read the mappings automatically from the directories following the order:
            #
            # 1. `config/serializer` - YAML or XML configuration files
            # 2. `src/Entity` - Annotations
            # 3. `src/Document` - Annotations
            # 4. `src/Model` - Annotations
            auto_configure: true

            # A list of paths and its namespaces like so:
            # - { namespace: "My\\Document", path: "%kernel.project_dir%/config/serializer" }
            paths: {  }

            cache:

                # Type of cache implementation. Should be one of "file", "psr" or "doctrine".
                type: file

                # Prefix of cache keys. Required only for "doctrine" and "psr" cache types.
                prefix: TSantosSerializer

                # Directory that will stored the metadata cache files. Required only for "file" cache type.
                path: "%kernel.cache_dir%/tsantos_serializer/metadata"
