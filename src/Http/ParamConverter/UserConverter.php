<?php

declare(strict_types=1);

namespace App\Http\ParamConverter;

use App\Doctrine\Entity\User;
use App\Doctrine\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

use function in_array;

final class UserConverter implements ParamConverterInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly UserRepository $userRepository
    ) {
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        if (in_array($request->getMethod(), [Request::METHOD_POST, Request::METHOD_PUT], true)) {
            $context = ['groups' => ['user:write']];

            if (Request::METHOD_PUT === $request->getMethod()) {
                /** @var User $user */
                $user = $this->userRepository->find($request->attributes->getInt('id'));

                $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $user;
            }

            $request->attributes->set(
                $configuration->getName(),
                $this->serializer->deserialize(
                    $request->getContent(),
                    User::class,
                    'json',
                    $context,
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
