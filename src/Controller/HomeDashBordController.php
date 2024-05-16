<?php

namespace App\Controller;

use App\Entity\File;
use App\Repository\CategoryRepository;
use App\Service\ImagesManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

class HomeDashBordController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository, ImagesManager $imageManager, EntityManagerInterface $em): Response
    {
        $targetDirectory = $imageManager->getTargetDirectory();
        $categories = $categoryRepository->findAll();
        $products = $productRepository->findAll();
        $images = $em->getRepository(File::class)->findAll();

        return $this->render('home_dash_bord/index.html.twig', [
            'products' => $products,
            'controller_name' => 'HomeController',
            'categories' => $categories,
            'target_directory' => $targetDirectory,
            'images' => $images
        ]);

    }
}
