# TSantos Serializer Bundle [![Build Status](https://travis-ci.org/tsantos84/serializer-bundle.svg?branch=master)](https://travis-ci.org/tsantos84/serializer-bundle)

This bundle integrates the TSantos Serializer into Symfony applications

## Installation

You can install this library through composer:

`composer require tsantos/serializer-bundle`

or just add `tsantos/serializer-bundle` to your composer file and then

`composer update`

Symfony Flex recipe: comming soon!

## Usage

Configure your application with the following configuration:

```yaml
# ./packages/tsantos_serializer.yml
tsantos_serializer: ~
```

    Thanks to the `auto-configure` option, the default configuration is enough to get your application running in most scenarios.
After that, you can access the serializer service through the name `tsantos_serializer`:

```php
<?php

namespace App\Controller;

use App\Document\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function index()
    {
        $person = new Person(100, 'Tales Santos');
        return JsonResponse::fromJsonString($this->get('tsantos_serializer')->serialize($person));
    }
}
```

## Configuration Reference

#### format `default: json`

The encode/decode format. Currently this bundle supports only format JSON. Please, refer to this documentation to see how you can
create and use custom encoders.

#### debug `default: %kernel.debug%`

Toggle between production optimizations or not.

#### class_path `default: %kernel.cache_dir%/tsantos_serializer/classes`

Directory which will generated the serializer classes

#### generate_strategy `default: file_not_found`

Strategy used to generate the serializer classes. Can be `never`, `always` and `file_not_found`.

`never` means that the serializer classes will never be generated (best for production environment)
`always` means that every time a new class will be generated (best for debugging)
`file_not_found` means that the serializer classes will be generated only if the class not exists (best for development environment)

#### mapping.auto_configure `default: file`

Auto-configure the application with the most common standards of entity, document and model organization. It will look for the `config/serializer` directory and add
that metadata path to the serializer's drivers. If your application does not follow any of those convention you'll need to configure
those paths manually. Refer to `mapping.paths` option to se the how configure the metadata paths:

#### mapping.paths

A list of mappings containing both path and its namespace.

```yaml
# ./packages/tsantos_serializer.yml
tsantos_serializer:
    mapping:
        paths:
            - { namespace: "My\\Document", path: "%kernel.project_dir%/config/serializer" }
```

#### mapping.cache.type `default: file`

Type of the cache used. Can be `file`, `doctrine`, `psr`.

#### mapping.cache.path `default: %kernel.cache_dir%/tsantos_serializer/metadata`

The directory which will store the metadata cache. Applied only for cache with type `file`.

#### mapping.cache.id

The id of service implementing cache interface. Applied only for cache with type `doctrine` or `psr`

#### mapping.cache.prefix `default: TSantosSerializer`

The cache prefix used to generate the keys (e.g: Redis key). Applied only for cache with type `doctrine` or `psr`

## Extending the bundle

This bundle can be extended by adding new encoders and normalizers.

### Encoders

Encoders are services that transform data from string to array and vice-versa. Although the TSantos Serializer Library currently supports only JSON encoder,
you can register new encoders to your application by implementing the `EncoderInterface`, register the service and tag it like following:

```php
<?php
// ./src/Serializer/JsonEncoder
namespace App\Serializer;

use TSantos\Serializer\Encoder\EncoderInterface;

class JsonEncoder implements EncoderInterface
{
    public function encode(array $data): string
    {
        return json_encode($data);
    }

    public function decode(string $content): array
    {
        return json_decode($content, true);
    }

    public function getFormat(): string
    {
        return 'json';
    }
}
```

Now you can tag your encoder and you are done!

```yaml
# ./services.yml
services:
    App\Serializer\JsonEncoder:
        tags:
            - { name: "tsantos_serializer.encoder", format: "format" }
```

### (De)normalizers

(De)normalizers are powerful services that transforms data without encoding/decoding them. For example,
supposing your entity has a date/time field. In this case, you don't need to create a mapping for \DateTime. Instead, all you need is to create your custom (de)normalizers and tag it like bellow.

```yaml
# ./services.yml
services:
    App\Serializer\Normalizer\DateTime:
        tags:
            - { name: "tsantos_serializer.normalizer" }
            - { name: "tsantos_serializer.denormalizer" }
```

    Please, refer to TSantos Serializer Library repository for a detailed documentation about the normalization process.
