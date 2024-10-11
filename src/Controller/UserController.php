<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/api/users', name: 'api_users', methods: ['GET'])]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/api/user/{id}/to/editor', name: 'api_user_to_editor', methods: ['PATCH'])]
    public function changeRole(EntityManagerInterface $entityManager, User $user): JsonResponse
    {
        $user->setRoles(["ROLE_EDITOR", "ROLE_USER"]);
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Le rôle éditeur a été ajouté']);
    }

    #[Route('/api/user/{id}/remove', name: 'api_user_remove', methods: ['DELETE'])]
    public function userRemove(EntityManagerInterface $entityManager, $id, UserRepository $userRepository): JsonResponse
    {
        $userFind = $userRepository->find($id);

        if (!$userFind) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        $entityManager->remove($userFind);
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Utilisateur supprimé']);
    }
}