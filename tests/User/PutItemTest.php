<?php

declare(strict_types=1);

namespace App\Tests\User;

use App\Doctrine\Entity\User;
use App\Tests\WebTestCaseHelperTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class PutItemTest extends WebTestCase
{
    use WebTestCaseHelperTrait;

    public function testShouldUpdateUser(): void
    {
        $client = static::createClient();

        $this->login();

        $this->put('/api/users/1', self::createUser());

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->find(1);

        self::assertSame('firstName', $user->getFirstName());
        self::assertSame('lastName', $user->getLastName());
    }

    public function testShouldReturn401(): void
    {
        static::createClient();

        $this->put('/api/users/1', self::createUser());

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testShouldReturn404DueToUserNotRelatedToAuthenticatedClient(): void
    {
        static::createClient();

        $this->login();

        $this->put('/api/users/26', self::createUser());

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
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

        $this->put('/api/users/1', $data);

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
