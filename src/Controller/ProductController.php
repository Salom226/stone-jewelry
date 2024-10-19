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
            limit: $request->query->getInt('limit', 10)
        );

        // dd($products);
    
    
        // $productArray = [];
        foreach ($products as $product) {
            $productArray[] = [
                'image' => $product->getImage(),
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
            ];
        }

        // $categoryArray = [];
        // $categories = $this->categoryRepository->findAll();
        // foreach ($categories as $category) {
        //     $categoryArray[] = [
        //         'id' => $category->getId(),
        //         'name' => $category->getName(),
        //     ];
        // }
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
        $idsStr = $request->query->get('ids'); // Un tableau d'IDs envoyé dans la requête
        $ids = $this->convertComaSeparatedToArray($idsStr);
        
        if(empty($ids)){
            return $this->json([]);
        }

        // dd($ids);
        $products = $productRepository->findBy(['id' => $ids]);

        // dd($products);
        
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
    
    // #[Route('/', name: 'app_product_index', methods: ['GET'])]
    // public function index(ProductRepository $productRepository): Response
    // {
    //     return $this->render('product/index.html.twig', [
    //         'products' => $productRepository->findAll(),
    //     ]);
    // }

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
    //     $product = new Product();
    //     $form = $this->createForm(ProductType::class, $product);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $image = $form->get('image')->getdata();

    //         if ($image){
    //             $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
    //             $safeFileName = $slugger->slug($originalName);
    //             $newFileName = $safeFileName.'-'.uniqid().'.'.$image->guessExtension();   
            
    //             try{
    //                 $image->move(
    //                     $this->getParameter(name:'image_dir'),
    //                     $newFileName
    //                 );
    //             }catch (FileException $exception){}

    //             $product->setImage($newFileName);
    //         }
    //         $entityManager->persist($product);
    //         $entityManager->flush();

    //         $stockHistory = new AddProductHistory();
    //         $stockHistory->setQte($product->getStock());
    //         $stockHistory->setProduct($product);
    //         $stockHistory->setCreatedAt(new \DateTimeImmutable());
    //         $entityManager->persist($stockHistory);
    //         $entityManager->flush();

    //         $this->addFlash(type: 'success', message:'Votre produit a été ajouté');
    //         return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('product/new.html.twig', [
    //         'product' => $product,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    // public function show(Product $product): Response
    // {
    //     return $this->render('product/show.html.twig', [
    //         'product' => $product,
    //     ]);
    // }

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

    #[Route('/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(Product $product)
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json([]);
    }
    #[Route('/add/product/{id}/stock', name: 'app_product_stock_add', methods: ['POST'])]
    public function addStock($id)
    {
        return $this->json([]);

    //     $addStock = new AddProductHistory();
    //     $form = $this->createForm(AddProductHistoryType::class,$addStock);
    //     $form->handleRequest($request);

    //     $product = $productRepository->find($id);

    //     if ($form->isSubmitted() && $form->isValid()){

    //         if ($addStock->getQte()>0){
    //             $newQte = $product->getStock() + $addStock->getQte();
    //             $product->setStock($newQte);
                
    //             $addStock->setCreatedAt(new \DateTimeImmutable());
    //             $addStock->setProduct($product);
    //             $entityManager->persist($addStock);
    //             $entityManager->flush();

    //             $this->addFlash("success", message:"Le stock de votre produit a été modifié");
    //             return $this->redirectToRoute('app_product_index');
    //         } else {
    //             $this->addFlash("danger","Le stock ne doit pas être inférieur à 1");
    //             return $this->redirectToRoute("app_product_stock_add",['id'=>$product->getId()]);
    //         }
    //     }

    //     return $this->render('product/addStock.html.twig',
    //         ['form' => $form->createView(),
    //         'product'=>$product
    //         ]
    //     );
    }
    // #[Route('/add/product/{id}/stock/history', name: 'app_product_stock_add_history', methods: ['GET'])]
    // public function productAddHistory($id, ProductRepository $productRepository, AddProductHistoryRepository $addProductHistoryRepository):Response
    // {
    //     $product = $productRepository->find($id);
    //     $productAddedHistory = $addProductHistoryRepository->findBy(['product'=>$product],['id'=>'DESC']);

    //     return $this->render('product/addedStockHistoryShow.html.twig', [
    //         'productsAdded'=>$productAddedHistory
    //     ]);
    // }
}
