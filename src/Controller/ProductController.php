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
use Symfony\Component\HttpFoundation\Response;
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
        $products = $this->productRepository->findBy([], ['id' => 'DESC']);
        $paginatedProducts = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            limit: $request->query->getInt('limit', 6)
        );
        $productArray = [];
    
        foreach ($paginatedProducts as $product) {
            $productArray[] = [
                'images' => $product->getImages(),
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
                'category' => [
                'id' => $product->getCategory()->getId(),
                'name' => $product->getCategory()->getName()
            ]
            ];
        }

        return new JsonResponse([
            'products' => $this->serializer->normalize($productArray, 'json', ['groups' => 'product_list']),
            'pagination' => [
                'current_page' => $request->query->getInt('page', 1),
                'total_items' => $paginatedProducts->getTotalItemCount(),
                'items_per_page' => $paginatedProducts->getItemNumberPerPage(),
                'total_pages' => ceil($paginatedProducts->getTotalItemCount() / 6),
            ],
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_products', methods: ['GET'])]
    public function getProductsByCategory(int $id): JsonResponse
    {
        $category = $this->categoryRepository->find($id);
    
        if (!$category) {
            return new JsonResponse(['error' => 'Catégorie non trouvée'], Response::HTTP_NOT_FOUND);
        }
    
        $products = $this->productRepository->findBy(['category' => $category]);
    
        if (empty($products)) {
            return new JsonResponse(['message' => 'Aucun produit trouvé dans cette catégorie'], Response::HTTP_OK);
        }
    
        return new JsonResponse([
            'category' => $category->getName(),
            'products' => array_map(fn($p) => [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'images' => $p->getImages(),
                'price' => $p->getPrice(),
            ], $products),
        ], Response::HTTP_OK);
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

    #[Route('/search', name: 'search_products', methods:['GET'])]
    public function searchProducts(Request $request): Response
    {
        $searchQuery = $request->query->get('q', '');
        $products = $this->productRepository->findBySearchQuery($searchQuery);

        return $this->json(
            $products,
            Response::HTTP_OK,
            [],
            ['groups' => ['product_simple', 'category_list']] 
        );
    }


    #[Route('/{id}', name: 'app_home_product_show', methods: ['GET'])]
    public function getProductDetail(Product $product, ProductRepository $productRepository, CategoryRepository $categoryRepository): JsonResponse
    {
        
        $lastProducts = $productRepository->findBy([], ['id' => 'DESC'], limit: 5);
    
        $lastProductsArray = [];
        foreach ($lastProducts as $lastProduct) {
            $lastProductsArray[] = [
                'id' => $lastProduct->getId(),
                'name' => $lastProduct->getName(),
                'price' => $lastProduct->getPrice(),
                'description' => $lastProduct->getDescription(),
                'images' => $lastProduct->getImages(),
            ];
        }
    
        $productArray = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
            'images' => $product->getImages(),
            'stock' => $product->getStock(),
            'category' => [
                'id' => $product->getCategory()->getId(),
                'name' => $product->getCategory()->getName()
            ]
        ];
    
        return new JsonResponse([
            'product' => $productArray,
            'last_products' => $lastProductsArray,
            'categories' => $this->categoriesToArray($categoryRepository->findAll()),
        ]);
    }
    
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
