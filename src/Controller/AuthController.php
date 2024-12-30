<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;


class AuthController extends AbstractController
{
    

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Security $security): JsonResponse
    {
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        return new JsonResponse([
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
    }

}
