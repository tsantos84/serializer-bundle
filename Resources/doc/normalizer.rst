Normalizer / Denormalizer
=========================

(De)normalizers are powerful services that transforms the data without encoding/decoding them. For example, supposing
your entity has a `date/time` field. In this case, you don't need to create a custom mapping for :phpclass:`\DateTime`
class to define how to configure such data type. Instead, all you need is to create your custom (de)normalizers::

    <?php

    namespace App\Serializer;

    use TSantos\Serializer\Normalizer\DenormalizerInterface;
    use TSantos\Serializer\Normalizer\NormalizerInterface;
    use TSantos\Serializer\DeserializationContext;
    use TSantos\Serializer\SerializationContext;

    class DateTimeNormalizer implements NormalizerInterface, DenormalizerInterface
    {
        private $format;

        public function __construct(string $format = \DateTime::ATOM)
        {
            $this->format = $format;
        }

        public function normalize($data, SerializationContext $context)
        {
            if (!$data instanceof \DateTimeInterface) {
                throw new InvalidArgumentException('Data should be instance of ' . \DateTimeInterface::class);
            }

            return $data->format($this->format);
        }

        public function supportsNormalization($data, SerializationContext $context): bool
        {
            return $data instanceof \DateTimeInterface;
        }

        public function denormalize($data, DeserializationContext $context)
        {
            return \DateTime::createFromFormat($this->format, $data);
        }

        public function supportsDenormalization(string $type, $data, DeserializationContext $context): bool
        {
            return $type === \DateTime::class || $type === \DateTimeInterface::class;
        }
    }



This bundle automatically recognize services that implements :class:`Normalizer\\NormalizerInterface` and
:class:`Normalizer\\DenormalizeInterface` and tag them with the proper tag name.

.. seealso::

    Please, refer to TSantos Serializer Library repository for a detailed documentation about the normalization process
    and the built-in normalizers.

.. note::

    If you are using a Symfony version prior to 3.3, you'll need to register and tag the normalizer services manually.

    .. code:: yaml

        services:
            App\Serializer\UserNormalizer:
                tags:
                    - { name: "tsantos_serializer.normalizer"}
                    - { name: "tsantos_serializer.denormalizer"}

.. tip::

    You can use normalizers to transform read-only entities and avoid unnecessary over-head due the serialization
    process::

        <?php

        namespace App\Serializer;

        use App\Entity\User;
        use App\Repository\UserRepository;
        use TSantos\Serializer\Normalizer\DenormalizerInterface;
        use TSantos\Serializer\Normalizer\NormalizerInterface;

        class UserNormalizer implements NormalizerInterface, DenormalizerInterface
        {
            private $userRepository;

            public function __construct(UserRepository $userRepository)
            {
                $this->userRepository = $userRepository;
            }

            public function normalize($data, SerializationContext $context)
            {
                return $data->getId();
            }

            public function supportsNormalization($data, SerializationContext $context): bool
            {
                return $data instanceof User;
            }

            public function denormalize($data, DeserializationContext $context)
            {
                return $this->userRepository->find($data);
            }

            public function supportsDenormalization(string $type, $data, DeserializationContext $context): bool
            {
                return $type === User::class;
            }
        }
