<?php

namespace App\Controller;

use App\Dto\CreateProductDto;
use App\Entity\Product;
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
    #[Route('/upload-multiple', name: 'app_product_images_upload', methods: ['POST'])]
public function uploadMultipleImages(Request $request, SluggerInterface $slugger): JsonResponse
{
    $files = $request->files->get('images');
    if (!is_array($files) || empty($files)) {
        return new JsonResponse(['error' => 'No files uploaded'], Response::HTTP_BAD_REQUEST);
    }

    $uploadsDirectory = $this->getParameter('image_dir');
    $imageUrls = [];

    foreach ($files as $file) {
        if (!$file instanceof UploadedFile) {
            continue;
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($uploadsDirectory, $newFilename);
            $imageUrls[] = $request->getSchemeAndHttpHost() . '/uploads/images/' . $newFilename;
        } catch (FileException $e) {
            return new JsonResponse(['error' => 'Failed to upload one or more files'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    return new JsonResponse(['imageUrls' => $imageUrls], Response::HTTP_CREATED);
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
       
        if (is_array($dto->images)) {
            $product->setImages($dto->images);
        } else {
            return $this->json(['error' => 'Invalid images format'], Response::HTTP_BAD_REQUEST);
        }

        $product->setStock($dto->stock);

        $this->entityManager->persist($product);
        $this->entityManager->flush();
        
        return $this->json($product, status: Response::HTTP_CREATED, context: ['groups' => 'product_simple']);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_product_edit', methods: ['PUT'])]
    public function edit(Product $product, #[MapRequestPayload()] CreateProductDto $dto, CategoryRepository $categoryRepository): JsonResponse
    {
        $category = $categoryRepository->find($dto->categoryId);

        $existingImages = $product->getImages();

        $imagesToDelete = array_diff($existingImages, $dto->images);
    
        foreach ($imagesToDelete as $imageFilename) {
            $imagePath = $this->getParameter('image_dir') . '/' . basename($imageFilename);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    
        $product->setCategory($category);
        $product->setName($dto->name);
        $product->setDescription($dto->description);
        $product->setPrice($dto->price);
        $product->setImages($dto->images); 
        $product->setStock($dto->stock);
    
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    
        return $this->json($product, context: ['groups' => 'product_simple']);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(Product $product): JsonResponse
    {
        $imageFilenames = $product->getImages();

        if ($imageFilenames) {
            foreach ($imageFilenames as $imageFilename) { 
                $imagePath = $this->getParameter('image_dir') . '/' . basename($imageFilename);
                
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json(['message' => 'Produit et images supprimés avec succès']);
    }
}
