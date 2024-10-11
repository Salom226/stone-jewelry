<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SubCategory;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


class HomeController extends AbstractController
{
//     #[Route('/', name: 'app_home', methods: ['GET'])]
// public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator): JsonResponse
// {
//     // Fetch products sorted by ID in descending order
//     $data = $productRepository->findBy([], ['id' => 'DESC']);

//     // Paginate the products, 6 per page
//     $products = $paginator->paginate(
//         $data,
//         $request->query->getInt('page', 2),
//         6
//     );

//     // Transform the products into an array
//     $productArray = [];
//     foreach ($products as $product) {
//         $productArray[] = [
//             'image' => $product->getImage(),
//             'id' => $product->getId(),
//             'name' => $product->getName(),
//             'price' => $product->getPrice(),
//             'description' => $product->getDescription(),
//             // Add more fields as needed
//         ];
//     }

//     // Fetch categories and transform them into an array
//     $categoryArray = [];
//     $categories = $categoryRepository->findAll();
//     foreach ($categories as $category) {
//         $categoryArray[] = [
//             'id' => $category->getId(),
//             'name' => $category->getName(),
//             // Add more fields if needed
//         ];
//     }

//     // Return the products and categories as JSON
//     return new JsonResponse([
//         'products' => $productArray,
//         'categories' => $categoryArray,
//         'pagination' => [
//             'current_page' => $request->query->getInt('page', 1),
//             'total_items' => $products->getTotalItemCount(),
//             'items_per_page' => $products->getItemNumberPerPage(),
//             'total_pages' => ceil($products->getTotalItemCount() / 6),
//         ],
//     ]);
// }

// #[Route('/home/product/{id}/show', name: 'app_home_product_show', methods: ['GET'])]
// public function show(Product $product, ProductRepository $productRepository, CategoryRepository $categoryRepository): JsonResponse
// {
//     // Récupérer les 5 derniers produits
//     $lastProducts = $productRepository->findBy([], ['id' => 'DESC'], limit: 5);

//     // Transformer les produits en tableau
//     $lastProductsArray = [];
//     foreach ($lastProducts as $lastProduct) {
//         $lastProductsArray[] = [
//             'id' => $lastProduct->getId(),
//             'name' => $lastProduct->getName(),
//             'price' => $lastProduct->getPrice(),
//             'description' => $lastProduct->getDescription(),
//             'image' => $lastProduct->getImage(),
//         ];
//     }

//     // Transformer le produit en tableau
//     $productArray = [
//         'id' => $product->getId(),
//         'name' => $product->getName(),
//         'price' => $product->getPrice(),
//         'description' => $product->getDescription(),
//         'image' => $product->getImage(),
//     ];

//     return new JsonResponse([
//         'product' => $productArray,
//         'last_products' => $lastProductsArray,
//         'categories' => $this->categoriesToArray($categoryRepository->findAll()),
//     ]);
// }

// // Helper function pour transformer les catégories en tableau
// private function categoriesToArray($categories) {
//     $categoryArray = [];
//     foreach ($categories as $category) {
//         $categoryArray[] = [
//             'id' => $category->getId(),
//             'name' => $category->getName(),
//         ];
//     }
//     return $categoryArray;
// }
}
