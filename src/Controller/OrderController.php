<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Entity\OrderProducts;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\Cart;
use App\Service\StripePayment;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    public function __construct(private MailerInterface $mailer){}
    
    #[Route('/api/cart-details', name: 'api_cart_details', methods: ['POST'])]
    public function getCartDetails()
    {
        return $this->json([]);

        // Récupérer le contenu JSON de la requête (IDs de produits)
        // $data = json_decode($request->getContent(), true);
        // $productIds = $data['productIds'] ?? [];

        // if (empty($productIds)) {
        //     return new JsonResponse(['message' => 'Le panier est vide.'], 400);
        // }

        // // Récupérer les produits depuis la base de données par leurs IDs
        // $products = $productRepository->findBy(['id' => $productIds]);

        // if (!$products) {
        //     return new JsonResponse(['message' => 'Aucun produit trouvé.'], 404);
        // }

        // // Construire la réponse avec les détails des produits
        // $cartDetails = [];
        // foreach ($products as $product) {
        //     $cartDetails[] = [
        //         'id' => $product->getId(),
        //         'name' => $product->getName(),
        //         'price' => $product->getPrice(),
        //         'image' => $product->getImage(),
        //         // Ajoute d'autres détails si nécessaire (description, etc.)
        //     ];
        // }

        // return new JsonResponse($cartDetails, 200);
    }
    
    
    // #[Route('/order', name: 'app_order')]
    // public function index(Request $request, SessionInterface $session, EntityManagerInterface $entityManager, ProductRepository $productRepository, Cart $cart, OrderRepository $orderRepository): Response
    // {
    //     $data = $cart->getCart($session);

    //     $order = new Order();
    //     $form = $this->createForm(OrderType::class, $order);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()){
    //         if(!empty($data['total'])){
                    
    //             $order->setTotalPrice($data['total']);
    //             $order->setCreatedAt(new \DateTimeImmutable());
    //             $entityManager->persist($order);
    //             $entityManager->flush();

    //             foreach ($data['cart'] as $value){
    //                 $orderProduct = new OrderProducts();
    //                 $orderProduct->setOrder($order);
    //                 $orderProduct->setProduct($value['product']);
    //                 $orderProduct->setQte($value['quantity']);
    //                 $entityManager->persist($orderProduct);
    //                 $entityManager->flush();
    //             }
    //         }
    //         $session->set('cart',[]);
        
    //     $payment = new StripePayment();

    //     $shippingCost = $order->getCity()->getShippingCost();
    //     $payment->startPayment($data,$shippingCost);
    //     $stripeRedirectUrl = $payment->getStripeRedirectUrl();
    //     return $this->redirect($stripeRedirectUrl);

    //     $html = $this->renderView('mail/orderConfirm.html.twig',[
    //         'order'=>$order
    //     ]);
    //     $email = (new Email())
    //     ->from('StoneJewelry@gmail.com')
    //     ->to($order->getEmail())
    //     ->subject('Confirmation de réception de la commande')
    //     ->html($html);

    //     $this->mailer->send($email);
    //     return $this->redirectToRoute('order_ok_message');
    //     }

    //     return $this->render('order/index.html.twig', [
    //         'form'=>$form->createView(),
    //         'total'=>$data['total']
    //     ]);
    // }
    #[Route('/api/orders', name: 'api_orders', methods: ['POST'])]
    public function createOrder()
    {
    return $this->json([]);

    // $data = json_decode($request->getContent(), true);

    // $order = new Order();
    // $order->setFirstName($data['firstName']);
    // $order->setLastName($data['lastName']);
    // $order->setEmail($data['email']);
    // $order->setPhone($data['phone']);
    // $order->setAdress($data['adress']);
    // $order->setCity($entityManager->getRepository(City::class)->find($data['city']));
    // $order->setCreatedAt(new \DateTimeImmutable());
    
    // $entityManager->persist($order);
    // $entityManager->flush();

    // return new JsonResponse(['status' => 'Order created successfully'], 201);
// }
//     #[Route('/editor/order', name: 'app_orders_show')]
    // public function getAllOrder(OrderRepository $orderRepository, Request $request, PaginatorInterface $paginator):Response{
        // $data = $orderRepository->findBy([],['id'=>'DESC']);
        // $order = $paginator->paginate(
        //     $data,
        //     $request->query->getInt('page', 1),
        //     5
        // );
        // return $this->render('order/order.html.twig', [
        //     "orders"=>$order
        // ]);
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