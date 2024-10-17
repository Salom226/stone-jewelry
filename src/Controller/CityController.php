<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/editor/city')]
class CityController extends AbstractController
{
    #[Route('/', name: 'app_city_index', methods: ['GET'])]
    public function index()
    {
        return $this->json([]);

        // return $this->render('city/index.html.twig', [
        //     'cities' => $cityRepository->findAll(),
        // ]);
    }

    #[Route('editor/city/new', name: 'app_city_new', methods: ['GET', 'POST'])]
    public function new()
    {
        return $this->json([]);

        // $city = new City();
        // $form = $this->createForm(CityType::class, $city);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $entityManager->persist($city);
        //     $entityManager->flush();

        //     return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
        // }

        // return $this->render('city/new.html.twig', [
        //     'city' => $city,
        //     'form' => $form,
        // ]);
    }

    // #[Route('/{id}', name: 'app_city_show', methods: ['GET'])]
    // public function show(City $city): Response
    // {
    //     return $this->render('city/show.html.twig', [
    //         'city' => $city,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_city_edit', methods: ['GET', 'POST'])]
    public function edit()
    {
        return $this->json([]);

        // $form = $this->createForm(CityType::class, $city);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $entityManager->flush();

        //     return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
        // }

        // return $this->render('city/edit.html.twig', [
        //     'city' => $city,
        //     'form' => $form,
        // ]);
    }

    #[Route('/{id}', name: 'app_city_delete', methods: ['DELETE'])]
    public function delete()
    {
        return $this->json([]);

        // if ($this->isCsrfTokenValid('delete'.$city->getId(), $request->getPayload()->getString('_token'))) {
        //     $entityManager->remove($city);
        //     $entityManager->flush();
        // }

        // return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
    }
}
