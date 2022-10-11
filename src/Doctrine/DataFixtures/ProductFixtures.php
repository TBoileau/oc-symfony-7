<?php

declare(strict_types=1);

namespace App\Doctrine\DataFixtures;

use App\Doctrine\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class ProductFixtures extends Fixture
{
    use FakerTrait;

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 100; ++$i) {
            /** @var string $name */
            $name = $this->faker()->words(3, true);

            /** @var string $description */
            $description = $this->faker()->paragraphs(3, true);

            $manager->persist(
                (new Product())
                    ->setName($name)
                    ->setReference(sprintf('REF-%04d', $i))
                    ->setDescription($description)
                    ->setPrice($this->faker()->numberBetween(5000, 100000))
                    ->setBrand($this->faker()->company())
            );
        }

        $manager->flush();
    }
}
