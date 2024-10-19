<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


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
public function login(Request $request): JsonResponse
{ 
    $data = json_decode($request->getContent(), true);
    $email = $data['username'];
    $password = $data['password'];
    

    // Chargez l'utilisateur par email
    $user = $this->userRepository->findOneBy(['email' => $email]);


    
    // dd($users);
    
    
    // Vérifiez si l'utilisateur existe
    if (!$user) {
        return new JsonResponse(['error' => 'Invalid credentials'], 401);
    }
    
    
    if (!$this->passwordHasher->isPasswordValid($user, $password)) {
        return new JsonResponse(['error' => 'Invalid credentials'], 401);
    }
    
    
    $token = $this->jwtManager->create($user);
    
    return new JsonResponse([
        'token' => $token,
        'roles' => $user->getRoles()
    ]);
}
    // #[Route(path: '/login', name: 'app_login')]
    // public function login(AuthenticationUtils $authenticationUtils): Response
    // {
    //     // if ($this->getUser()) {
    //     //     return $this->redirectToRoute('target_path');
    //     // }

    //     // get the login error if there is one
    //     $error = $authenticationUtils->getLastAuthenticationError();
    //     // last username entered by the user
    //     $lastUsername = $authenticationUtils->getLastUsername();

    //     return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    // }

    
    // #[Route(path: '/api/testage', name: 'chips', methods: ['POST'])]
    // public function test(): JsonResponse
    // {
    //     return new JsonResponse(['status' => 'OK']);
    // }

    // #[Route(path: '/logout', name: 'app_logout')]
    // public function logout(): void
    // {
    //     throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    // }
}
