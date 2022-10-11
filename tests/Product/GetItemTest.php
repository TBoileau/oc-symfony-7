<?php

declare(strict_types=1);

namespace App\Tests\Product;

use App\Tests\WebTestCaseHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class GetItemTest extends WebTestCase
{
    use WebTestCaseHelperTrait;

    public function testShouldReturnProduct(): void
    {
        $client = static::createClient();

        $this->login();

        $this->get('/api/products/1');

        self::assertResponseIsSuccessful();

        /** @var string $content */
        $content = $client->getResponse()->getContent();

        self::assertJson($content);

        /** @var array<string, mixed> $data */
        $data = json_decode($content, true);

        self::assertArrayHasKey('id', $data);
        self::assertSame(1, $data['id']);

        self::assertArrayHasKey('name', $data);
        self::assertArrayHasKey('reference', $data);
        self::assertArrayHasKey('brand', $data);
        self::assertArrayHasKey('description', $data);
        self::assertArrayHasKey('price', $data);
        self::assertArrayHasKey('tax', $data);
        self::assertArrayHasKey('_links', $data);
        self::assertSame([
            'self' => [
                'href' => 'http://localhost/api/products/1',
            ],
        ], $data['_links']);
    }

    public function testShouldReturn401(): void
    {
        static::createClient();

        $this->get('/api/products/1');

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testShouldReturn404(): void
    {
        static::createClient();

        $this->login();

        $this->get('/api/products/101');

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
