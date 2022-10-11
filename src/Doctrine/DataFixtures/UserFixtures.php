<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Doctrine\Entity\Client;
use App\Doctrine\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class UserFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [ClientFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        /** @var array<array-key, Client> $clients */
        $clients = $manager->getRepository(Client::class)->findAll();

        foreach ($clients as $client) {
            for ($i = 1; $i <= 25; ++$i) {
                $manager->persist(
                    (new User())
                        ->setFirstName($this->faker()->firstName())
                        ->setLastName($this->faker()->lastName())
                        ->setClient($client)
                );
            }
        }

        $manager->flush();
    }
}
