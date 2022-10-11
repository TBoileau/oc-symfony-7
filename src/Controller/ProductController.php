<?php

declare(strict_types=1);

namespace App\Controller;

use App\Doctrine\Entity\Product;
use App\Doctrine\Repository\ProductRepository;
use App\Hal\CollectionFactoryInterface;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Tag('Product')]
#[Route('/products', name: 'product_')]
final class ProductController extends AbstractController
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
        description: 'The number of products per page',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer'),
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns the collection of products',
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
                            property: 'products',
                            type: 'array',
                            items: new Items(
                                properties: [
                                    new OA\Property(
                                        property: 'id',
                                        type: 'integer',
                                    ),
                                    new OA\Property(
                                        property: 'name',
                                        type: 'string',
                                    ),
                                    new OA\Property(
                                        property: 'reference',
                                        type: 'string',
                                    ),
                                    new OA\Property(
                                        property: 'brand',
                                        type: 'string',
                                    ),
                                    new OA\Property(
                                        property: 'description',
                                        type: 'string',
                                    ),
                                    new OA\Property(
                                        property: 'price',
                                        type: 'integer',
                                    ),
                                    new OA\Property(
                                        property: 'tax',
                                        type: 'float',
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
        ProductRepository $productRepository,
        CollectionFactoryInterface $collectionFactory
    ): JsonResponse {
        $limit = $request->query->getInt('limit', 10);

        $page = $request->query->getInt('page', 1);

        $products = $productRepository->findBy([], ['name' => 'asc'], $limit, ($page - 1) * $limit);

        return $this->json(
            $collectionFactory->create(
                'products',
                $products,
                $page,
                $limit,
                $productRepository->count([]),
                'product_get_collection',
            ),
            Response::HTTP_OK,
            ['content-type' => 'application/hal+json'],
        );
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns product',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'id',
                    type: 'integer',
                ),
                new OA\Property(
                    property: 'name',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'reference',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'brand',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'price',
                    type: 'integer',
                ),
                new OA\Property(
                    property: 'tax',
                    type: 'float',
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
                        ],
                        type: 'object',
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
    public function getItem(Product $product): JsonResponse
    {
        return $this->json(
            $product,
            Response::HTTP_OK,
            ['content-type' => 'application/hal+json']
        );
    }
}
