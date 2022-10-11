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
}
