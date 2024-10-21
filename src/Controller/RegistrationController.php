<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\SecurityAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/api/login/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true); 

        $user = new User();
        $user->setEmail($data['email']); 
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $data['plainPassword'] 
            )
        );
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Utilisateur enregistré avec succès']);
    }
}