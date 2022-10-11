<?php

declare(strict_types=1);

namespace App\Tests\User;

use App\Tests\WebTestCaseHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class GetItemTest extends WebTestCase
{
    use WebTestCaseHelperTrait;

    public function testShouldReturnUser(): void
    {
        $client = static::createClient();

        $this->login();

        $this->get('/api/users/1');

        self::assertResponseIsSuccessful();

        /** @var string $content */
        $content = $client->getResponse()->getContent();

        self::assertJson($content);

        /** @var array<string, mixed> $data */
        $data = json_decode($content, true);

        self::assertArrayHasKey('id', $data);
        self::assertSame(1, $data['id']);

        self::assertArrayHasKey('firstName', $data);
        self::assertArrayHasKey('lastName', $data);
        self::assertArrayHasKey('_links', $data);
        self::assertSame([
            'self' => [
                'href' => 'http://localhost/api/users/1',
            ],
            'delete' => [
                'href' => 'http://localhost/api/users/1',
            ],
            'update' => [
                'href' => 'http://localhost/api/users/1',
            ],
        ], $data['_links']);
    }

    public function testShouldReturn401(): void
    {
        static::createClient();

        $this->get('/api/users/1');

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testShouldReturn404DueToUserNotRelatedToAuthenticatedClient(): void
    {
        static::createClient();

        $this->login();

        $this->get('/api/users/26');

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testShouldReturn404(): void
    {
        static::createClient();

        $this->login();

        $this->get('/api/users/126');

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
