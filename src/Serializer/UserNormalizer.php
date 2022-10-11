<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Doctrine\Entity\User;
use App\Hal\Serializer\ItemNormalizer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @template-extends ItemNormalizer<User>
 */
final class UserNormalizer extends ItemNormalizer
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator, ObjectNormalizer $normalizer)
    {
        parent::__construct($normalizer);
    }

    protected function getLinks($object): array
    {
        return [
            'self' => [
                'href' => $this->urlGenerator->generate(
                    'user_get_item',
                    ['id' => $object->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ],
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof User;
    }
}
