<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\CartType;
use App\Repository\CartRepository;
use App\Repository\ProductCartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart_index', methods: ['GET'])]
    public function index(CartRepository $cartRepository): Response
    {
        $user = $this->getUser();

        $carts = $cartRepository->findBy([
            'user' => $user
        ]);

        return $this->render('cart/index.html.twig', [
            'carts' => $carts
        ]);
    }


    #[Route('/new', name: 'app_cart_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cart = new Cart();
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cart);
            $entityManager->flush();

            return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cart/new.html.twig', [
            'cart' => $cart,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cart_show', methods: ['GET'])]
    public function show(Cart $cart, ProductCartRepository $productCartRepo): Response
    {
        
        $productCarts = $productCartRepo->findBy([
            'cart' => $cart
        ]);

        $totalCart = 0;

        return $this->render('cart/show.html.twig', [
            'cart' => $cart,
            'productCarts' => $productCarts
        ]);
    }

    #[Route('/{id}', name: 'app_cart_delete', methods: ['POST'])]
    public function delete(Request $request, ProductCartRepository $productCartRepo, CartRepository $cartRepo, EntityManagerInterface $entityManager): Response
    {
        $cart = $cartRepo->findOneBy([
            'id' => $request->get('id')
        ]);

        $productCarts = $productCartRepo->findBy([
            'cart' => $cart
        ]);

        foreach($productCarts as $productCart){
            $entityManager->remove($productCart);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cart_show', ['id' => $request->get('id')], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/validate', name: 'app_cart_validate', methods: ['GET', 'POST'])]
    public function validate(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        $cart->setSave(true);

        $entityManager->persist($cart);
        $entityManager->flush();

        return $this->redirectToRoute('app_cart_show', ['id' => $request->get('id')], Response::HTTP_SEE_OTHER);
    }
}
