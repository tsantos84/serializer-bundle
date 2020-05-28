TSantos Serializer Bundle ![TSantos Serializer Bundle](https://github.com/tsantos84/serializer-bundle/workflows/TSantos%20Serializer%20Bundle/badge.svg)
=========================

Installation 
============

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require tsantos/serializer-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require tsantos/serializer-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
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
```

Documentation
=============

Please, visit the [documentation page](http://tsantos-serializer-bundle.readthedocs.io/)
to read the detailed information about this bundle.

Tests
=====

```console
$ composer test
```

License
=======

* MIT
