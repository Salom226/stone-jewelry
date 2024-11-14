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
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
    #[Route('/upload', name: 'app_product_image_upload', methods: ['POST'])]
    public function uploadImage(Request $request, SluggerInterface $slugger): JsonResponse
    {
    $file = $request->files->get('image');
    
    if (!$file instanceof UploadedFile) {
        return new JsonResponse(['error' => 'No file uploaded'], Response::HTTP_BAD_REQUEST);
    }

    $uploadsDirectory = $this->getParameter('image_dir');
    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $safeFilename = $slugger->slug($originalFilename);
    $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

    try {
        $file->move($uploadsDirectory, $newFilename);
    } catch (FileException $e) {
        return new JsonResponse(['error' => 'Failed to upload file'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    $imageUrl = $request->getSchemeAndHttpHost() . '/uploads/images/' . $newFilename;
    return new JsonResponse(['imageUrl' => $imageUrl], Response::HTTP_CREATED);
}
    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'app_product_new', methods: ['POST'])]
    public function newProduct(
        #[MapRequestPayload()] CreateProductDto $dto,
        CategoryRepository $categoryRepository
    )
    {
        $product = new Product();
        $category = $categoryRepository->find($dto->categoryId);
        if (!$category) {
        throw $this->createNotFoundException('Catégorie non trouvée');
        }
        $product->setCategory($category);
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
        $imageFilename = $product->getImage();

        if ($imageFilename) {
            $imagePath = $this->getParameter('image_dir') . '/' . basename($imageFilename);
            
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json(['message' => 'Produit et image supprimés avec succès']);
    }
}
