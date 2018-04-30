Encoder / Decoder
=================

Encoders are services that transform data from string to array and vice-versa. Although the TSantos Serializer Library
currently supports only JSON encoder, you can register new encoders to your application by implementing the
:class:`Encoder\\EncoderInterface` interface, register the service in the container and tag it like following::

    // src/Serializer/JsonEncoder
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

Now you can tag your encoder and you are done to use your custom encoder.

.. code-block:: yaml

    # ./config/services.yml
    services:
        App\Serializer\JsonEncoder:
            tags:
                - { name: "tsantos_serializer.encoder", format: "format" }

.. note::

    The attribute `format` is required! This value will be matched against the option `tsantos_serializer.format` on
    your configuration to define which encoder will be used in your application.

.. note::

    Different from the most used serializer libraries, you don't need to pass the format on `$serializer->serialize(...)`
    calls.
