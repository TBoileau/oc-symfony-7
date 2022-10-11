<?php

declare(strict_types=1);

namespace App\Controller;

use App\Doctrine\Entity\Client;
use App\Doctrine\Entity\User;
use App\Doctrine\Repository\UserRepository;
use App\Hal\CollectionFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/users', name: 'user_')]
final class UserController extends AbstractController
{
    #[Route(name: 'get_collection', methods: [Request::METHOD_GET])]
    public function getCollection(
        Request $request,
        UserRepository $userRepository,
        CollectionFactoryInterface $collectionFactory
    ): JsonResponse {
        /** @var Client $client */
        $client = $this->getUser();

        $limit = $request->query->getInt('limit', 10);

        $page = $request->query->getInt('page', 1);

        $users = $userRepository->findBy(
            ['client' => $client],
            ['id' => 'asc'],
            $limit,
            ($page - 1) * $limit
        );

        return $this->json(
            $collectionFactory->create(
                'users',
                $users,
                $page,
                $limit,
                $userRepository->count(['client' => $client]),
                'user_get_collection',
            ),
            Response::HTTP_OK,
            ['content-type' => 'application/hal+json'],
        );
    }

    #[Route('/{id}', name: 'get_item', methods: [Request::METHOD_GET])]
    #[IsGranted('', subject: 'user')]
    public function getItem(User $user): JsonResponse
    {
        return $this->json(
            $user,
            Response::HTTP_OK,
            ['content-type' => 'application/hal+json'],
            ['groups' => ['user:read']]
        );
    }

    #[Route(name: 'post_collection', methods: [Request::METHOD_POST])]
    public function postCollection(
        User $user,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse {
        $violations = $validator->validate($user);

        if ($violations->count() > 0) {
            throw new ValidationFailedException($user, $violations);
        }

        /** @var Client $client */
        $client = $this->getUser();

        $user->setClient($client);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            $user,
            Response::HTTP_CREATED,
            [
                'content-type' => 'application/hal+json',
                'location' => $this->generateUrl(
                    'user_get_item',
                    ['id' => $user->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ],
            ['groups' => ['user:read']]
        );
    }

    #[Route('/{id}', name: 'put_item', methods: [Request::METHOD_PUT])]
    #[IsGranted('', subject: 'user')]
    public function putItem(
        User $user,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse {
        $violations = $validator->validate($user);

        if ($violations->count() > 0) {
            throw new ValidationFailedException($user, $violations);
        }

        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
