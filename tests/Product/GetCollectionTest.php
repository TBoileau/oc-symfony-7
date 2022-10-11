<?php

declare(strict_types=1);

namespace App\Tests\Product;

use App\Tests\WebTestCaseHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class GetCollectionTest extends WebTestCase
{
    use WebTestCaseHelperTrait;

    /**
     * @param array{
     *      self: array{href: string},
     *      first?: array{href: string},
     *      last?: array{href: string},
     *      next?: array{href: string},
     *      previous?: array{href: string}
     * } $links
     *
     * @dataProvider provideProducts
     */
    public function testShouldReturnCollectionOfProducts(
        int $page,
        int $limit,
        int $pages,
        int $total,
        int $count,
        array $links
    ): void {
        $client = static::createClient();

        $this->login();

        $this->get('/api/products', [
            'page' => $page,
            'limit' => $limit,
        ]);

        self::assertResponseIsSuccessful();

        /** @var string $content */
        $content = $client->getResponse()->getContent();

        self::assertJson($content);

        /**
         * @var array{
         *      page: int,
         *      pages: int,
         *      limit: int,
         *      total: int,
         *      count: int,
         *      _links: array{
         *          self: array{href: string},
         *          first?: array{href: string},
         *          last?: array{href: string},
         *          next?: array{href: string},
         *          previous?: array{href: string}
         *      },
         *      _embedded: array{products: array<array-key, array<array-key, mixed>>}
         * } $data
         */
        $data = json_decode($content, true);

        ksort($links);
        ksort($data['_links']);

        self::assertSame($page, $data['page']);
        self::assertSame($limit, $data['limit']);
        self::assertSame($pages, $data['pages']);
        self::assertSame($total, $data['total']);
        self::assertSame($count, $data['count']);
        self::assertEquals($links, $data['_links']);
        self::assertCount($count, $data['_embedded']['products']);
    }

    /**
     * @return iterable<string, array{
     *      page: int,
     *      pages: int,
     *      limit: int,
     *      total: int,
     *      count: int,
     *      _links: array{
     *          self: array{href: string},
     *          first?: array{href: string},
     *          last?: array{href: string},
     *          next?: array{href: string},
     *          previous?: array{href: string}
     *      }
     * }>
     */
    public function provideProducts(): iterable
    {
        yield 'page 1' => [
            'page' => 1,
            'limit' => 10,
            'pages' => 10,
            'total' => 100,
            'count' => 10,
            '_links' => [
                'self' => [
                    'href' => 'http://localhost/api/products?page=1&limit=10',
                ],
                'next' => [
                    'href' => 'http://localhost/api/products?page=2&limit=10',
                ],
                'last' => [
                    'href' => 'http://localhost/api/products?page=10&limit=10',
                ],
            ],
        ];

        yield 'page 2' => [
            'page' => 2,
            'limit' => 10,
            'pages' => 10,
            'total' => 100,
            'count' => 10,
            '_links' => [
                'self' => [
                    'href' => 'http://localhost/api/products?page=2&limit=10',
                ],
                'next' => [
                    'href' => 'http://localhost/api/products?page=3&limit=10',
                ],
                'last' => [
                    'href' => 'http://localhost/api/products?page=10&limit=10',
                ],
                'previous' => [
                    'href' => 'http://localhost/api/products?page=1&limit=10',
                ],
                'first' => [
                    'href' => 'http://localhost/api/products?page=1&limit=10',
                ],
            ],
        ];

        yield 'page 10' => [
            'page' => 10,
            'limit' => 10,
            'pages' => 10,
            'total' => 100,
            'count' => 10,
            '_links' => [
                'self' => [
                    'href' => 'http://localhost/api/products?page=10&limit=10',
                ],
                'previous' => [
                    'href' => 'http://localhost/api/products?page=9&limit=10',
                ],
                'first' => [
                    'href' => 'http://localhost/api/products?page=1&limit=10',
                ],
            ],
        ];
    }

    public function testShouldReturn401(): void
    {
        static::createClient();

        $this->get('/api/products');

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
