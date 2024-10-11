<?php

namespace App\Controller;

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
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('', name: 'app_home', methods: ['GET'])]
    public function getAllProducts(ProductRepository $productRepository, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator): JsonResponse
    {
        // Fetch products sorted by ID in descending order
        $data = $productRepository->findBy([], ['id' => 'DESC']);
    
        // Paginate the products, 6 per page
        $products = $paginator->paginate(
            $data,
            $request->query->getInt('page', 2),
            6
        );
    
        $productArray = [];
        foreach ($products as $product) {
            $productArray[] = [
                'image' => $product->getImage(),
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'description' => $product->getDescription(),
            ];
        }

        $categoryArray = [];
        $categories = $categoryRepository->findAll();
        foreach ($categories as $category) {
            $categoryArray[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
            ];
        }
            return new JsonResponse([
            'products' => $productArray,
            'categories' => $categoryArray,
            'pagination' => [
                'current_page' => $request->query->getInt('page', 1),
                'total_items' => $products->getTotalItemCount(),
                'items_per_page' => $products->getItemNumberPerPage(),
                'total_pages' => ceil($products->getTotalItemCount() / 6),
            ],
        ]);
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

    #[Route('editor/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getdata();

            if ($image){
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalName);
                $newFileName = $safeFileName.'-'.uniqid().'.'.$image->guessExtension();   
            
                try{
                    $image->move(
                        $this->getParameter(name:'image_dir'),
                        $newFileName
                    );
                }catch (FileException $exception){}

                $product->setImage($newFileName);
            }
            $entityManager->persist($product);
            $entityManager->flush();

            $stockHistory = new AddProductHistory();
            $stockHistory->setQte($product->getStock());
            $stockHistory->setProduct($product);
            $stockHistory->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($stockHistory);
            $entityManager->flush();

            $this->addFlash(type: 'success', message:'Votre produit a été ajouté');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    // public function show(Product $product): Response
    // {
    //     return $this->render('product/show.html.twig', [
    //         'product' => $product,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductUpdateType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getdata();

            if ($image){
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalName);
                $newFileName = $safeFileName.'-'.uniqid().'.'.$image->guessExtension();   
            
                try{
                    $image->move(
                        $this->getParameter(name:'image_dir'),
                        $newFileName
                    );
                }catch (FileException $exception){}

                $product->setImage($newFileName);
            }

            $entityManager->flush();

            $this->addFlash(type: 'success', message:'Votre produit a été modifié');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $this->addFlash(type: 'danger', message:'Votre produit a été supprimé');

            $entityManager->flush();
        }
        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/add/product/{id}/stock', name: 'app_product_stock_add', methods: ['POST','GET'])]
    public function addStock($id, EntityManagerInterface $entityManager, Request $request, ProductRepository $productRepository):Response
    {
        $addStock = new AddProductHistory();
        $form = $this->createForm(AddProductHistoryType::class,$addStock);
        $form->handleRequest($request);

        $product = $productRepository->find($id);

        if ($form->isSubmitted() && $form->isValid()){

            if ($addStock->getQte()>0){
                $newQte = $product->getStock() + $addStock->getQte();
                $product->setStock($newQte);
                
                $addStock->setCreatedAt(new \DateTimeImmutable());
                $addStock->setProduct($product);
                $entityManager->persist($addStock);
                $entityManager->flush();

                $this->addFlash("success", message:"Le stock de votre produit a été modifié");
                return $this->redirectToRoute('app_product_index');
            } else {
                $this->addFlash("danger","Le stock ne doit pas être inférieur à 1");
                return $this->redirectToRoute("app_product_stock_add",['id'=>$product->getId()]);
            }
        }

        return $this->render('product/addStock.html.twig',
            ['form' => $form->createView(),
            'product'=>$product
            ]
        );
    }
    #[Route('/add/product/{id}/stock/history', name: 'app_product_stock_add_history', methods: ['GET'])]
    public function productAddHistory($id, ProductRepository $productRepository, AddProductHistoryRepository $addProductHistoryRepository):Response
    {
        $product = $productRepository->find($id);
        $productAddedHistory = $addProductHistoryRepository->findBy(['product'=>$product],['id'=>'DESC']);

        return $this->render('product/addedStockHistoryShow.html.twig', [
            'productsAdded'=>$productAddedHistory
        ]);
    }
}
