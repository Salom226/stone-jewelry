<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository){}

    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index(SessionInterface $session, Cart $cart): Response
    {
        $data = $cart->getCart($session); // Récupère les données du panier
    
    // Pour débogage : afficher les données du panier
    // dump($data);
    // exit;     
    // dd($session);

        if (empty($data['cart'])) {
            return new JsonResponse([
                'items' => [],
                'total' => 0,
                'shippingCost' => 0
            ], Response::HTTP_OK);
        }

        // Retourne les données du panier en JSON
        return $this->json([
            'items' => $data['cart'],
            'total' => $data['total'],
            'shippingCost' => 0 // Ou calculez vos frais de livraison ici
        ]);
    }
    #[Route('/cart/add/{id}/', name: 'app_cart_new', methods: ['POST'])]
    public function addToCart(int $id, SessionInterface $session): Response
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            return new JsonResponse(['error' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $cart = $session->get('cart',[]);
        if (isset($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart',$cart);
        return new JsonResponse([
            'success' => true,
            'cart' => $cart
        ], Response::HTTP_OK);
    }
    #[Route('/cart/remove/{id}/', name: 'app_cart_product_remove', methods: ['GET'])]
    public function removeToCart($id, SessionInterface $session):Response
    {
        $cart = $session->get('cart',[]);
        if (!empty($cart[$id])){
            unset($cart[$id]);
        }
        $session->set('cart',$cart);
        
        return $this->redirectToRoute('app_cart');
    }
    #[Route('/cart/remove', name: 'app_cart_remove', methods: ['GET'])]
    public function remove(SessionInterface $session):Response
    {
        $session->set('cart',[]);
        return $this->redirectToRoute('app_cart');
    }
}