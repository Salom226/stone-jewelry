<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderProductsRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin/orders')]
class OrderAdminController extends AbstractController
{
    public function __construct(
        private MailerInterface $mailer, 
        private EntityManagerInterface $entityManager,
        private OrderRepository $orderRepository,
        private OrderProductsRepository $orderProductsRepository
    ){}

    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'all_orders', methods: ['GET'])]
    public function getAllOrders(Request $request)
    {
        $orders = $this->orderRepository->findAll();
        $data = [];

        foreach ($orders as $order) {
            $data[] = [
                'id' => $order->getId(),
                'email' => $order->getEmail(),
                'phone' => $order->getPhone(),
                'address' => $order->getAdress(),
                'totalPrice' => $order->getTotalPrice(),
                'createdAt' => $order->getCreatedAt()->format('d-m-Y H:i:s'),
                'firstName' => $order->getFirstName(),
                'lastName' => $order->getLastName(),
                'completed' => $order->isCompleted()
            ];
        }

        return new JsonResponse($data);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'order_details', methods: ['GET'])]
    public function getOrder(Order $order)
    {
        $products = $this->orderProductsRepository->findBy(['_order' => $order]);
        $productsArray = [];
        foreach ($products as $product) {
            $productsArray[] = [
                'id' => $product->getProduct()->getId(),
                'name' => $product->getProduct()->getName(),
                'price' => $product->getProduct()->getPrice(),
                'quantity' => $product->getQte()
            ];
        }

        $orderArray = [
            'id' => $order->getId(),
            'email' => $order->getEmail(),
            'phone' => $order->getPhone(),
            'address' => $order->getAdress(),
            'totalPrice' => $order->getTotalPrice(),
            'createdAt' => $order->getCreatedAt()->format('d-m-Y H:i:s'),
            'firstName' => $order->getFirstName(),
            'lastName' => $order->getLastName(),
            'products' => $productsArray
        ];

        return new JsonResponse($orderArray);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/validate', name: 'validate', methods: ['PUT'])]
    public function validateOrder(Order $order)
    {
        $order->setCompleted(true);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Order validated']);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function deleteOrder(Order $order)
    {
        $this->entityManager->remove($order);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Order deleted']);
    }

} 