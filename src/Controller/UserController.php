<?php

namespace App\Controller;

use App\Dto\CreateUserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/users')]
class UserController extends AbstractController
{

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    )
    {
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'api_users', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $users = $this->userRepository->findAll();
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

    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'api_users_create', methods: ['POST'])]
    public function createUser(
        #[MapRequestPayload] CreateUserDto $dto
    ): JsonResponse
    {
        $existingUser = $this->userRepository->findOneBy(['email' => $dto->username]);

        if ($existingUser) {
            return new JsonResponse(['error' => 'User already exists'], 400);
        }

        $user = new User();
        $user->setEmail($dto->username);
        $user->setPassword($dto->password);
        $user->setRoles($dto->roles);
        $user->setFirstName($dto->firstName);
        $user->setLastName($dto->lastName);
    

        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $dto->password
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'User created']);
    }
    

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'api_users_edit', methods: ['PATCH'])]
    public function editUser(): JsonResponse
    {
        $users = $this->userRepository->findAll();
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

    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'api_users_delete', methods: ['DELETE'])]
    public function deleteUser(User $user): JsonResponse
    {

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $this->userRepository->delete($user, true);

        return new JsonResponse(['success' => true, 'message' => 'User deleted']);
    }
}