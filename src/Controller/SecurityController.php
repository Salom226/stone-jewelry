<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Psr\Log\LoggerInterface;

class SecurityController extends AbstractController
{
    private $jwtManager;
    private $userRepository;
    private $passwordHasher;

    public function __construct(JWTTokenManagerInterface $jwtManager, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {

        $this->jwtManager = $jwtManager;

        $this->userRepository = $userRepository;

        $this->passwordHasher = $passwordHasher;
    }

    
    #[Route("/api/login", name: "api_login", methods: ["POST"])]
    public function login(Request $request, LoggerInterface $logger): JsonResponse
    { 
        $data = json_decode($request->getContent(), true);
        $email = $data['username'];
        $password = $data['password'];
        
        // Charge l'utilisateur par email
        $user = $this->userRepository->findOneBy(['email' => $email]);
        
        // Vérifie si l'utilisateur existe
        if (!$user) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }
        
        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }
        
        $logger->info('User object before token generation', ['user' => $user->getEmail()]);
        $token = $this->jwtManager->create($user);
        $logger->info('Generated token', ['token' => $token]);
        
        return new JsonResponse([
            'token' => $token,
            'roles' => $user->getRoles()
        ]);
    }
}
