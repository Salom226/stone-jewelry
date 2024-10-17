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

class CategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'app_category', methods:['GET'])]
    public function getCategory()
    {
            return $this->json([]);
    }
        // $categories = $categoryRepository->findAll();

        // $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // return $this->render('category/index.html.twig', [
        //     'categories'=>$categories
        // ]);
    // }
    
    #[Route('/admin/category/new', name: 'app_category_new', methods:['POST'])]
    public function addCategory()
    {
        return $this->json([]);
        // $category = new Category();

        // $form = $this->createForm(CategoryFormType::class, $category);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {

        //     $entityManager->persist($category);
        //     $entityManager->flush();

        //     $this->addFlash(type: 'success',message: "votre catégorie à été ajouté");
        //     return $this->redirectToRoute(route:'app_category');

        // }
    
        // return $this->render('category/new.html.twig', [
        //     'form' => $form->createView(),
        // ]);
    }
    #[Route('/admin/category/{id}/update', name: 'app_category_update', methods:['UPDATE'])]
    public function update()
    {
        return $this->json([]);

        // $form = $this->createForm(CategoryFormType::class, $category);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()){
        //     $entityManager->flush();

        //     $this->addFlash(type: 'success',message: "votre catégorie à été modifié");

        //     return $this->redirectToRoute(route:'app_category');

        // }

        // return $this->render('category/update.html.twig', [
        //     'form' => $form->createView(),
        // ]);
    }
    #[Route('/admin/category/{id}/delete', name: 'app_category_delete', methods:['DELETE'])]
    public function categoryDelete()
    {
        return $this->json([]);


        // $entityManager->remove($category);
        // $entityManager->flush(); 

        // $this->addFlash(type: 'success',message: "votre catégorie à été supprimé");

        // return $this->redirectToRoute(route:'app_category');
    }
}

