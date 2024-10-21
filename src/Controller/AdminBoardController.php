<?php

namespace App\Controller;

use App\Dto\CreateUserDto;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/board')]
class AdminBoardController extends AbstractController
{

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly OrderRepository $orderRepository,
        private readonly ProductRepository $productRepository
    )
    {
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'api_admin_board', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $totalUsers = $this->userRepository->countUsers();
        $totalOrders = $this->orderRepository->countOrders();
        $totalProducts = $this->productRepository->countProducts();
        $data = [];

        $data = [
            'totalUsers' => $totalUsers,
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
        ];

        return new JsonResponse($data);
    }
}