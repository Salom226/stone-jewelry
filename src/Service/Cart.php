<?php

namespace App\Service;

use App\Repository\ProductRepository;
use App\Entity\City;

class Cart
{
    public function __construct(private readonly ProductRepository $productRepository) {}

    public function getCart($session): array
    {
        $cart = $session->get('cart', []);
        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);
            if ($product) {
                $cartWithData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
            }
        }

        // Calcule le total du panier
        $total = array_sum(array_map(function ($item) {
            return $item['product']->getPrice() * $item['quantity'];
        }, $cartWithData));

        return [
            'cart' => $cartWithData,
            'total' => $total
        ];
    }
    
    public function calculateShippingCost(City $city): float
    {
        // Logique pour calculer les frais de livraison en fonction de la ville
        return $city->getShippingCost(); 
    }
}