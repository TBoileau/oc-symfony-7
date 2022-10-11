<?php

declare(strict_types=1);

namespace App\Tests\User;

use App\Tests\WebTestCaseHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class PostCollectionTest extends WebTestCase
{
    use WebTestCaseHelperTrait;

    public function testShouldCreateUserAndReturnIt(): void
    {
        $client = static::createClient();

        $this->login();

        $this->post('/api/users', self::createUser());

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        /** @var string $content */
        $content = $client->getResponse()->getContent();

        self::assertJson($content);

        /** @var array<string, mixed> $data */
        $data = json_decode($content, true);

        self::assertArrayHasKey('id', $data);

        self::assertIsInt($data['id']);

        self::assertResponseHeaderSame('location', sprintf('http://localhost/api/users/%d', $data['id']));

        self::assertArrayHasKey('firstName', $data);
        self::assertSame('firstName', $data['firstName']);

        self::assertArrayHasKey('lastName', $data);
        self::assertSame('lastName', $data['lastName']);

        self::assertArrayHasKey('_links', $data);
        self::assertSame([
            'self' => [
                'href' => sprintf('http://localhost/api/users/%d', $data['id']),
            ],
            'delete' => [
                'href' => sprintf('http://localhost/api/users/%d', $data['id']),
            ],
            'update' => [
                'href' => sprintf('http://localhost/api/users/%d', $data['id']),
            ],
        ], $data['_links']);
    }

    public function testShouldReturn401(): void
    {
        static::createClient();

        $this->post('/api/users', self::createUser());

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param array{firstName: string, lastName: string} $data
     *
     * @dataProvider provideInvalidData
     */
    public function testShouldReturn422DueToInvalidData(array $data): void
    {
        static::createClient();

        $this->login();

        $this->post('/api/users', $data);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @return iterable<string, array<array-key, array{firstName: string, lastName: string}>>
     */
    public function provideInvalidData(): iterable
    {
        yield 'blank firstName' => [self::createUser(firstName: '')];
        yield 'blank lastName' => [self::createUser(lastName: '')];
    }

    /**
     * @return array{firstName: string, lastName: string}
     */
    private static function createUser(string $firstName = 'firstName', string $lastName = 'lastName'): array
    {
        return ['firstName' => $firstName, 'lastName' => $lastName];
    }
}
