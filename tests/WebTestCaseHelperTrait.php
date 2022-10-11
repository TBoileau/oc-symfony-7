<?php

declare(strict_types=1);

namespace App\Tests;

use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

use function json_encode;

trait WebTestCaseHelperTrait
{
    private static function getClient(): KernelBrowser
    {
        $method = new ReflectionMethod(WebTestCase::class, 'getClient');

        /** @var KernelBrowser $kernel */
        $kernel = $method->invoke(null);

        return $kernel;
    }

    /**
     * @param array<string, mixed> $body
     */
    public function post(string $uri, array $body = []): void
    {
        /** @var string $json */
        $json = json_encode($body);

        self::getClient()->request(
            Request::METHOD_POST,
            $uri,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $json
        );
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function get(string $uri, array $parameters = []): void
    {
        self::getClient()->request(
            Request::METHOD_GET,
            sprintf('%s?%s', $uri, http_build_query($parameters))
        );
    }

    public function login(string $apiKey = 'api-key-1'): void
    {
        $this->post('/api/login_check', [
            'apiKey' => $apiKey,
            'apiSecret' => 'secret',
        ]);

        /** @var string $content */
        $content = static::getClient()->getResponse()->getContent();

        /** @var array{token: string} $data */
        $data = json_decode($content, true);

        self::getClient()->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
    }
}
