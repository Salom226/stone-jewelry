<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProducts;
use App\Entity\Product;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/orders')]
class OrderController extends AbstractController
{
    public function __construct(
        private MailerInterface $mailer, 
        private EntityManagerInterface $entityManager
    ){}
    

    #[Route('', name: 'create_order', methods: ['POST'])]
    public function createOrder(Request $request)
    {
        $data = $request->toArray();

        $order = new Order();
        $order->setFirstName($data['firstName']);
        $order->setLastName($data['lastName']);
        $order->setEmail($data['email']);
        $order->setPhone($data['phone']);
        $order->setAdress($data['address']);
        $order->setCreatedAt(new DateTimeImmutable());

        $productIds = array_keys($data['cart']);

        $products = $this->entityManager->getRepository(Product::class)->findBy(['id'=>$productIds]);
        
        $totalPrice = 0;
        foreach ($products as $product){
            $quantity = $data['cart'][$product->getId()];
            $orderProduct = new OrderProducts();
            $orderProduct->setOrder($order);
            $orderProduct->setProduct($product);
            $orderProduct->setQte($quantity);

            $totalPrice += $product->getPrice() * $quantity;

            $this->entityManager->persist($orderProduct);
        }

        $order->setTotalPrice($totalPrice);
        

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $this->json($order, Response::HTTP_CREATED);
    }

    #[Route('/api/orders/{id}', name: 'api_orders_delete', methods: ['DELETE'])]
    public function deleteOrder()
    {
        return $this->json([]);
    }
} 