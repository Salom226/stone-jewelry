<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/categories')]
class CategoriesController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $em
    )
    {}
    
    
    #[Route('/', name: 'app_categories', methods:['GET'])]
    public function getCategories(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();
        
        return $this->json($categories);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'app_categories_add', methods:['POST'])]
    public function addCategory(Request $request)
    {
        $payload = $request->toArray();
        if(empty($payload['name'])){
            return $this->json(['error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
        }
        $existingCategory = $this->categoryRepository->findOneBy(['name' => $payload['name']]);
        if($existingCategory){
            return $this->json(['error' => 'Category with this name already exists'], Response::HTTP_CONFLICT);
        }
        $category = new Category();
        $category->setName($payload['name']);
        $this->em->persist($category);
        $this->em->flush();
        return $this->json($category, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_categories_update', methods:['PUT'])]
    public function updateCategory(Category $category, Request $request)
    {
        $payload = $request->toArray();
        if(empty($payload['name'])){
            return $this->json(['error' => 'Name is required'], Response::HTTP_BAD_REQUEST);
        }
        $category->setName($payload['name']);
        $this->em->flush(); 
        return $this->json($category, Response::HTTP_OK);
    }
    
    #[Route('/{id}', name: 'app_category_delete', methods:['DELETE'])]
    public function categoryDelete(Category $category)
    {
        $this->em->remove($category);
        $this->em->flush();
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}

