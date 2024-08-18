<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
           'users' =>$userRepository->findAll() ,
        ]);
    }
    #[Route('/admin/user/{id}/to/editor', name: 'app_user_to_editor')]
    public function changeRole(EntityManagerInterface $entityManager, User $user): Response
    {
        $user->setRoles(["ROLE_EDITOR","ROLE_USER"]);
        $entityManager->flush();

        $this->addFlash(type:'success',message:'le role éditeur a été ajouté à votre utilisateur');

        return $this->redirectToRoute(route:'app_user');
    }
    #[Route('/admin/user/{id}/remove/editor/role', name: 'app_user_remove_editor_role')]
    public function editorRoleRemove(EntityManagerInterface $entityManager, User $user): Response
    {
        $user->setRoles([]);
        $entityManager->flush();

        $this->addFlash(type:'success',message:'le role éditeur a été retiré à votre utilisateur');

        return $this->redirectToRoute(route:'app_user');
    }
    #[Route('/admin/user/{id}/remove/', name: 'app_user_remove')]
    public function userRemove(EntityManagerInterface $entityManager, $id, UserRepository $userRepository): Response
    {
        $userFind = $userRepository->find($id);
        
        $entityManager->remove($userFind);
        $entityManager->flush();

        $this->addFlash(type:'danger',message:'Votre utilisateur a été supprimé');

        return $this->redirectToRoute(route:'app_user');
    }
}