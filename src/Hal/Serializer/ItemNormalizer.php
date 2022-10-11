<?php

declare(strict_types=1);

namespace App\Hal\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @template T
 */
abstract class ItemNormalizer implements NormalizerInterface
{
    public function __construct(private readonly ObjectNormalizer $normalizer)
    {
    }

    /**
     * @param T $object
     *
     * @return array<string, array{href: string}>
     */
    abstract protected function getLinks($object): array;

    /**
     * @return array<string, mixed>
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $normalizeData = $this->normalizer->normalize($object, $format, $context);

        return $normalizeData + ['_links' => $this->getLinks($object)];
    }
}
