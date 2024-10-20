<?php

namespace App\Controller;

use App\Dto\CreateProductDto;
use App\Entity\AddProductHistory;
use App\Entity\Product;
use App\Form\AddProductHistoryType;
use App\Form\ProductType;
use App\Form\ProductUpdateType;
use App\Repository\AddProductHistoryRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/api/admin/products')]
class ProductAdminController extends AbstractController
{

    public function __construct(
        private ProductRepository $productRepository, 
        private CategoryRepository $categoryRepository, 
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
    )
    {
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'app_product_new', methods: ['POST'])]
    public function newProduct(
        #[MapRequestPayload()] CreateProductDto $dto
    )
    {
        $product = new Product();
        $product->setName($dto->name);
        $product->setDescription($dto->description);
        $product->setPrice($dto->price);
        $product->setImage($dto->image);
        $product->setStock($dto->stock);

        $this->entityManager->persist($product);
        $this->entityManager->flush();
        
        return $this->json($product, status: Response::HTTP_CREATED, context: ['groups' => 'product_simple']);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_product_edit', methods: ['PUT'])]
    public function edit(Product $product, #[MapRequestPayload()] CreateProductDto $dto): JsonResponse
    {
        $product->setName($dto->name);
        $product->setDescription($dto->description);
        $product->setPrice($dto->price);
        $product->setImage($dto->image);
        $product->setStock($dto->stock);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->json($product, context: ['groups' => 'product_simple']);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(Product $product)
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json([]);
    }
}
