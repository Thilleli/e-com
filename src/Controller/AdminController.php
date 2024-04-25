<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(entityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $userCount = $userRepository->count([]);
        $users = $userRepository->findAll();
        $productRepository = $entityManager->getRepository(Product::class);
        $productCount = $productRepository->count([]);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'userCount' => $userCount,
            'users' => $users,
            'productCount' => $productCount,
        ]);
    }
}
