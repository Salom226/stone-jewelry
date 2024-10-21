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
        // TODO: set city
        // $order->setCity($data['city']);

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

    //     $entityManager->remove($order);
    //     $entityManager->flush();

    //     return new JsonResponse(['status' => 'Order deleted successfully.']);
    }
    // #[Route('/editor/order/{id}/isCompleted/update', name: 'app_orders_is_completed_update')]
    // public function isCompletedUpdate($id, OrderRepository $orderRepository, EntityManagerInterface $entityManager):Response
    // {

    //     $order = $orderRepository->find($id);
    //     if (!$order) {
    //         // Add a flash message to notify the user about the missing order
    //         $this->addFlash('error', 'Order not found.');
    
    //         // Redirect back to the order list (or any other appropriate route)
    //         return $this->redirectToRoute('app_orders_show');
    //     }
    
    //     $order->setCompleted(true);
    //     $entityManager->flush();
    //     $this->addFlash('success','modification effectuée');
    //     return $this->redirectToRoute('app_orders_show');
    // }

    // #[Route('/editor/order/{id}/isCompleted/remove', name: 'app_orders_remove')]
    // public function removeOrder(Order $order, EntityManagerInterface $entityManager):Response
    // {
    //     $entityManager->remove($order);
    //     $entityManager->flush();
    //     $this->addFlash('danger', 'Votre commande a été supprimée');
    //     return $this->redirectToRoute('app_orders_show');    
    // }

    // #[Route("order-ok-message", name: 'order_ok_message')]
    // public function orderMessage():Response{
    //     return $this->render('order/order_message.html.twig');
    // }

    // #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    // public function cityShippingCost()
    // {
        // $this->json([]);

    //     $cityShippingPrice = $city->getShippingCost();

    //     return new Response(json_encode(['status'=>200, "message"=>'on', 'content'=>$cityShippingPrice]));
    // }
} 