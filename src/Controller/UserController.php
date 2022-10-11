<?php

declare(strict_types=1);

namespace App\Controller;

use App\Doctrine\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users', name: 'user_')]
final class UserController extends AbstractController
{
    #[Route('/{id}', name: 'get_item', methods: [Request::METHOD_GET])]
    #[IsGranted('', subject: 'user')]
    public function getItem(User $user): JsonResponse
    {
        return $this->json(
            $user,
            Response::HTTP_OK,
            ['content-type' => 'application/hal+json'],
            ['groups' => ['user:read']]
        );
    }
}
