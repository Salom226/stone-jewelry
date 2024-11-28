<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/api/categories')]
class PublicCategoriesController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository
    )
    {}

    #[Route('', name: 'app_public_categories', methods:['GET'])]
    public function getCategories()
    {
        $categories = $this->categoryRepository->findAll();

        return $this->json(
            $categories,
            status: Response::HTTP_OK,
            context: ['groups' => ['category_list']]
        );
    }
    
}


