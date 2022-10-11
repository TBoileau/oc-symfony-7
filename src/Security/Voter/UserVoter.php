<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Doctrine\Entity\Client;
use App\Doctrine\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class UserVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $subject;

        /** @var Client $client */
        $client = $token->getUser();

        return $user->getClient() === $client;
    }
}
