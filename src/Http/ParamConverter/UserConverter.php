<?php

declare(strict_types=1);

namespace App\Http\ParamConverter;

use App\Doctrine\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

use function in_array;

final class UserConverter implements ParamConverterInterface
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        if (in_array($request->getMethod(), [Request::METHOD_POST, Request::METHOD_PUT], true)) {
            $request->attributes->set(
                $configuration->getName(),
                $this->serializer->deserialize(
                    $request->getContent(),
                    User::class,
                    'json',
                    ['groups' => ['user:write']],
                ),
            );

            return true;
        }

        return false;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return User::class === $configuration->getClass();
    }
}
