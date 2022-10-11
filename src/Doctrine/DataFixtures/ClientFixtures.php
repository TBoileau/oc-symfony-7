<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Doctrine\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use function sprintf;

final class ClientFixtures extends Fixture
{
    use FakerTrait;

    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $client = (new Client())
                ->setName($this->faker()->company())
                ->setApiKey(sprintf('api-key-%d', $i));

            $client->setApiSecret($this->userPasswordHasher->hashPassword($client, 'secret'));

            $manager->persist($client);
        }

        $manager->flush();
    }
}
