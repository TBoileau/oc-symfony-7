<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class LoginTest extends WebTestCase
{
    use WebTestCaseHelperTrait;

    public function testShouldReturnJwt(): void
    {
        $client = static::createClient();

        $this->post('/api/login_check', [
            'apiKey' => 'api-key-1',
            'apiSecret' => 'secret',
        ]);

        self::assertResponseIsSuccessful();

        /** @var string $content */
        $content = $client->getResponse()->getContent();

        self::assertJson($content);

        /** @var array<string, mixed> $data */
        $data = json_decode($content, true);

        self::assertArrayHasKey('token', $data);
    }

    /**
     * @param array<string, string> $credentials
     *
     * @dataProvider provideBadRequest
     */
    public function testShouldReturn400(array $credentials, string $message): void
    {
        $client = static::createClient();

        $this->post('/api/login_check', $credentials);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        /** @var string $content */
        $content = $client->getResponse()->getContent();

        self::assertJson($content);

        /** @var array<string, mixed> $data */
        $data = json_decode($content, true);

        self::assertArrayHasKey('code', $data);
        self::assertSame(Response::HTTP_BAD_REQUEST, $data['code']);

        self::assertArrayHasKey('message', $data);
        self::assertSame($message, $data['message']);
    }

    /**
     * @return iterable<string, array{credentials: array<string, string>, message: string}>
     */
    public function provideBadRequest(): iterable
    {
        yield 'missing apiKey' => [
            'credentials' => ['apiSecret' => 'secret'],
            'message' => 'The key "apiKey" must be provided.',
        ];
        yield 'missing apiSecret' => [
            'credentials' => ['apiKey' => 'api-key-1'],
            'message' => 'The key "apiSecret" must be provided.',
        ];
    }

    /**
     * @param array{apiKey: string, apiSecret: string} $credentials
     *
     * @dataProvider provideInvalidCredentials
     */
    public function testShouldReturn401DueToInvalidCredentials(array $credentials): void
    {
        $client = static::createClient();

        $this->post('/api/login_check', $credentials);

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        /** @var string $content */
        $content = $client->getResponse()->getContent();

        self::assertJson($content);

        /** @var array<string, mixed> $data */
        $data = json_decode($content, true);

        self::assertArrayHasKey('code', $data);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $data['code']);

        self::assertArrayHasKey('message', $data);
        self::assertSame('Invalid credentials.', $data['message']);
    }

    /**
     * @return iterable<string, array<array-key, array{apiKey: string, apiSecret: string}>>
     */
    public function provideInvalidCredentials(): iterable
    {
        yield 'wrong apiKey' => [self::createCredentials(apiKey: 'fail')];
        yield 'wrong apiSecret' => [self::createCredentials(apiSecret: 'fail')];
    }

    /**
     * @return array{apiKey: string, apiSecret: string}
     */
    private static function createCredentials(string $apiKey = 'api-key-1', string $apiSecret = 'secret'): array
    {
        return ['apiKey' => $apiKey, 'apiSecret' => $apiSecret];
    }
}
