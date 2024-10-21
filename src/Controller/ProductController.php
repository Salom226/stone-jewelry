<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/products')]
class ProductController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository, 
        private CategoryRepository $categoryRepository, 
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
    )
    {
    }

    #[Route('', name: 'app_home', methods: ['GET'])]
    public function getAllProducts(Request $request, PaginatorInterface $paginator): JsonResponse
    {
        // Fetch products sorted by ID in descending order
        $products = $this->productRepository->findBy([], ['id' => 'DESC']);
        // Paginate the products, 6 per page
        $paginatedProducts = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            limit: $request->query->getInt('limit', 6)
        );
    
        foreach ($paginatedProducts as $product) {
            $productArray[] = [
                'image' => $product->getImage(),
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
            ];
        }

        return new JsonResponse([
            'products' => $this->serializer->normalize($productArray, 'json'),
            // 'categories' => $categoryArray,
            'pagination' => [
                'current_page' => $request->query->getInt('page', 1),
                'total_items' => $paginatedProducts->getTotalItemCount(),
                'items_per_page' => $paginatedProducts->getItemNumberPerPage(),
                'total_pages' => ceil($paginatedProducts->getTotalItemCount() / 6),
            ],
        ]);
    }


    #[Route('/filtered', name: 'app_product_filtered', methods: ['GET'])]
    public function getFilteredProducts(Request $request, ProductRepository $productRepository)
    {
        $idsStr = $request->query->get('ids');
        $ids = $this->convertComaSeparatedToArray($idsStr);
        
        if(empty($ids)){
            return $this->json([]);
        }
        $products = $productRepository->findBy(['id' => $ids]);
        
        return $this->json($products, context: ['groups' => 'product_simple']);
    }
    

    private function convertComaSeparatedToArray(string $ids)
    {
        $separator = ',';
        return explode($separator, $ids);
    }


    #[Route('/{id}', name: 'app_home_product_show', methods: ['GET'])]
    public function getProductDetail(Product $product, ProductRepository $productRepository, CategoryRepository $categoryRepository): JsonResponse
    {
        
        // Récupérer les 5 derniers produits
        $lastProducts = $productRepository->findBy([], ['id' => 'DESC'], limit: 5);
    
        $lastProductsArray = [];
        foreach ($lastProducts as $lastProduct) {
            $lastProductsArray[] = [
                'id' => $lastProduct->getId(),
                'name' => $lastProduct->getName(),
                'price' => $lastProduct->getPrice(),
                'description' => $lastProduct->getDescription(),
                'image' => $lastProduct->getImage(),
            ];
        }
    
        $productArray = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
            'image' => $product->getImage(),
            'stock' => $product->getStock()
        ];
    
        return new JsonResponse([
            'product' => $productArray,
            'last_products' => $lastProductsArray,
            'categories' => $this->categoriesToArray($categoryRepository->findAll()),
        ]);
    }
    
    // Helper function pour transformer les catégories en tableau
    private function categoriesToArray($categories) {
        $categoryArray = [];
        foreach ($categories as $category) {
            $categoryArray[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
            ];
        }
        return $categoryArray;
    }
}
