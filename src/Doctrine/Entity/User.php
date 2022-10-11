<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

use App\Doctrine\Repository\UserRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;

#[Entity(repositoryClass: UserRepository::class)]
class User
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[Column]
    #[Groups(['user:read'])]
    private string $lastName;

    #[Column]
    #[Groups(['user:read'])]
    private string $firstName;

    #[ManyToOne]
    #[JoinColumn(nullable: true)]
    private Client $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): User
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): User
    {
        $this->client = $client;

        return $this;
    }
}
