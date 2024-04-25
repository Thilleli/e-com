<?php

namespace App\Controller;

use App\Entity\ProductCart;
use App\Repository\ProductRepository;
use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ProductCartController extends AbstractController
{
    #[Route('/product/cart', name: 'app_product_cart')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('product_cart/index.html.twig', [
            'controller_name' => 'ProductCartController',
        ]);
    }

}
