<?php

declare(strict_types=1);

namespace App\Tests\User;

use App\Doctrine\Entity\User;
use App\Tests\WebTestCaseHelperTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class DeleteItemTest extends WebTestCase
{
    use WebTestCaseHelperTrait;

    public function testShouldReturnUser(): void
    {
        $client = static::createClient();

        $this->login();

        $this->delete('/api/users/1');

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var ?User $user */
        $user = $entityManager->getRepository(User::class)->find(1);

        self::assertNull($user);
    }

    public function testShouldReturn401(): void
    {
        static::createClient();

        $this->delete('/api/users/1');

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testShouldReturn404DueToUserNotRelatedToAuthenticatedClient(): void
    {
        static::createClient();

        $this->login();

        $this->delete('/api/users/26');

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testShouldReturn404(): void
    {
        static::createClient();

        $this->login();

        $this->delete('/api/users/126');

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
