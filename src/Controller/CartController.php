<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository){}

    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index() {
        return $this->json([]);
    }
    #[Route('/cart/add/{id}/', name: 'app_cart_new', methods: ['POST'])]
    public function addToCart(){
            return $this->json([]);
    }
    #[Route('/cart/remove/{id}/', name: 'app_cart_product_remove', methods: ['DELETE'])]
        public function removeToCart(){
                return $this->json([]);
        }
      
    #[Route('/cart/remove', name: 'app_cart_remove', methods: ['DELETE'])]
    public function remove(){
        return $this->json([]);
    }
}