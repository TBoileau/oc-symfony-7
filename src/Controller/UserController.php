<?php

declare(strict_types=1);

namespace App\Controller;

use App\Doctrine\Entity\Client;
use App\Doctrine\Entity\User;
use App\Doctrine\Repository\UserRepository;
use App\Hal\CollectionFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag('User')]
#[Route('/users', name: 'user_')]
final class UserController extends AbstractController
{
    #[OA\Parameter(
        name: 'page',
        description: 'The page number',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer'),
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'The number of users per page',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer'),
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the collection of users',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'page',
                    type: 'integer',
                ),
                new OA\Property(
                    property: 'pages',
                    type: 'integer',
                ),
                new OA\Property(
                    property: 'limit',
                    type: 'integer',
                ),
                new OA\Property(
                    property: 'count',
                    type: 'integer',
                ),
                new OA\Property(
                    property: 'total',
                    type: 'integer',
                ),
                new OA\Property(
                    property: '_links',
                    type: 'array',
                    items: new Items(
                        properties: [
                            new Property(
                                property: 'self',
                                properties: [
                                    new Property(
                                        property: 'href',
                                        type: 'string',
                                    ),
                                ],
                                type: 'object',
                            ),
                            new Property(
                                property: 'post',
                                properties: [
                                    new Property(
                                        property: 'href',
                                        type: 'string',
                                    ),
                                ],
                                type: 'object',
                            ),
                            new Property(
                                property: 'first',
                                properties: [
                                    new Property(
                                        property: 'href',
                                        type: 'string',
                                    ),
                                ],
                                type: 'object',
                            ),
                            new Property(
                                property: 'next',
                                properties: [
                                    new Property(
                                        property: 'href',
                                        type: 'string',
                                    ),
                                ],
                                type: 'object',
                            ),
                            new Property(
                                property: 'previous',
                                properties: [
                                    new Property(
                                        property: 'href',
                                        type: 'string',
                                    ),
                                ],
                                type: 'object',
                            ),
                            new Property(
                                property: 'last',
                                properties: [
                                    new Property(
                                        property: 'href',
                                        type: 'string',
                                    ),
                                ],
                                type: 'object',
                            ),
                        ],
                        type: 'object',
                    )
                ),
                new OA\Property(
                    property: '_embedded',
                    properties: [
                        new Property(
                            property: 'users',
                            type: 'array',
                            items: new Items(
                                properties: [
                                    new OA\Property(
                                        property: 'id',
                                        type: 'integer',
                                    ),
                                    new OA\Property(
                                        property: 'firstName',
                                        type: 'string',
                                    ),
                                    new OA\Property(
                                        property: 'lastName',
                                        type: 'string',
                                    ),
                                    new OA\Property(
                                        property: '_links',
                                        type: 'array',
                                        items: new Items(
                                            properties: [
                                                new Property(
                                                    property: 'self',
                                                    properties: [
                                                        new Property(
                                                            property: 'href',
                                                            type: 'string',
                                                        ),
                                                    ],
                                                    type: 'object',
                                                ),
                                                new Property(
                                                    property: 'update',
                                                    properties: [
                                                        new Property(
                                                            property: 'href',
                                                            type: 'string',
                                                        ),
                                                    ],
                                                    type: 'object',
                                                ),
                                                new Property(
                                                    property: 'delete',
                                                    properties: [
                                                        new Property(
                                                            property: 'href',
                                                            type: 'string',
                                                        ),
                                                    ],
                                                    type: 'object',
                                                ),
                                            ],
                                            type: 'object',
                                        )
                                    ),
                                ],
                                type: 'object'
                            ),
                        ),
                    ],
                    type: 'object',
                ),
            ],
            type: 'object'
        ),
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: 'Unauthorized',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'code',
                    type: 'integer',
                    example: 401
                ),
                new OA\Property(
                    property: 'message',
                    type: 'string',
                ),
            ],
            type: 'object'
        ),
    )]
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
            )->addLinks(
                'post',
                $this->generateUrl(
                    'user_post_collection',
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ),
            Response::HTTP_OK,
            ['content-type' => 'application/hal+json'],
        );
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the collection of users',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                ),
                new OA\Property(
                    property: 'firstName',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'lastName',
                    type: 'string',
                ),
                new OA\Property(
                    property: '_links',
                    type: 'array',
                    items: new Items(
                        properties: [
                            new Property(
                                property: 'self',
                                properties: [
                                    new Property(
                                        property: 'href',
                                        type: 'string',
                                    ),
                                ],
                                type: 'object',
                            ),
                            new Property(
                                property: 'update',
                                properties: [
                                    new Property(
                                        property: 'href',
                                        type: 'string',
                                    ),
                                ],
                                type: 'object',
                            ),
                            new Property(
                                property: 'delete',
                                properties: [
                                    new Property(
                                        property: 'href',
                                        type: 'string',
                                    ),
                                ],
                                type: 'object',
                            ),
                        ],
                    )
                ),
            ],
            type: 'object'
        ),
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'User not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'code',
                    type: 'integer',
                    example: 404
                ),
                new OA\Property(
                    property: 'message',
                    type: 'string',
                ),
            ],
            type: 'object'
        ),
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: 'Unauthorized',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'code',
                    type: 'integer',
                    example: 401
                ),
                new OA\Property(
                    property: 'message',
                    type: 'string',
                ),
            ],
            type: 'object'
        ),
    )]
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

    #[OA\RequestBody(
        request: 'User',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'firstName',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'lastName',
                    type: 'string',
                ),
            ],
            type: 'object'
        ),
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the collection of users',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                ),
                new OA\Property(
                    property: 'firstName',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'lastName',
                    type: 'string',
                ),
                new OA\Property(
                    property: '_links',
                    type: 'array',
                    items: new Items(
                        properties: [
                            new Property(
                                property: 'self',
                                properties: [
                                    new Property(
                                        property: 'href',
                                        type: 'string',
                                    ),
                                ],
                                type: 'object',
                            ),
                            new Property(
                                property: 'update',
                                properties: [
                                    new Property(
                                        property: 'href',
                                        type: 'string',
                                    ),
                                ],
                                type: 'object',
                            ),
                            new Property(
                                property: 'delete',
                                properties: [
                                    new Property(
                                        property: 'href',
                                        type: 'string',
                                    ),
                                ],
                                type: 'object',
                            ),
                        ],
                    )
                ),
            ],
            type: 'object'
        ),
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: 'Unauthorized',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'code',
                    type: 'integer',
                    example: 401
                ),
                new OA\Property(
                    property: 'message',
                    type: 'string',
                ),
            ],
            type: 'object'
        ),
    )]
    #[OA\Response(
        response: Response::HTTP_UNPROCESSABLE_ENTITY,
        description: 'Unprocessable entity',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'code',
                    type: 'integer',
                    example: 422
                ),
                new OA\Property(
                    property: 'message',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'violations',
                    type: 'array',
                    items: new Items(
                        properties: [
                            new Property(
                                property: 'propertyPath',
                                type: 'string',
                            ),
                            new Property(
                                property: 'message',
                                type: 'string',
                            ),
                        ],
                    )
                ),
            ],
            type: 'object'
        ),
    )]
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

    #[OA\RequestBody(
        request: 'User',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'firstName',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'lastName',
                    type: 'string',
                ),
            ],
            type: 'object'
        ),
    )]
    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'No content'
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: 'Unauthorized',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'code',
                    type: 'integer',
                    example: 401
                ),
                new OA\Property(
                    property: 'message',
                    type: 'string',
                ),
            ],
            type: 'object'
        ),
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'User not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'code',
                    type: 'integer',
                    example: 404
                ),
                new OA\Property(
                    property: 'message',
                    type: 'string',
                ),
            ],
            type: 'object'
        ),
    )]
    #[OA\Response(
        response: Response::HTTP_UNPROCESSABLE_ENTITY,
        description: 'Unprocessable entity',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'code',
                    type: 'integer',
                    example: 422
                ),
                new OA\Property(
                    property: 'message',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'violations',
                    type: 'array',
                    items: new Items(
                        properties: [
                            new Property(
                                property: 'propertyPath',
                                type: 'string',
                            ),
                            new Property(
                                property: 'message',
                                type: 'string',
                            ),
                        ],
                    )
                ),
            ],
            type: 'object'
        ),
    )]
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

    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'No content'
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: 'Unauthorized',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'code',
                    type: 'integer',
                    example: 401
                ),
                new OA\Property(
                    property: 'message',
                    type: 'string',
                ),
            ],
            type: 'object'
        ),
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'User not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'code',
                    type: 'integer',
                    example: 404
                ),
                new OA\Property(
                    property: 'message',
                    type: 'string',
                ),
            ],
            type: 'object'
        ),
    )]
    #[Route('/{id}', name: 'delete_item', methods: [Request::METHOD_DELETE])]
    #[IsGranted('', subject: 'user')]
    public function deleteItem(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
