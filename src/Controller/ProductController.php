<?php

declare(strict_types=1);

namespace App\Controller;

use App\Doctrine\Entity\Product;
use App\Doctrine\Repository\ProductRepository;
use App\Hal\CollectionFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products', name: 'product_')]
final class ProductController extends AbstractController
{
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
