<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Cart;
use App\Entity\ProductCart;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CartRepository;
use App\Repository\ProductCartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/product/category/{id}', name: 'app_product_by_category', methods: ['GET'])]
    public function productsByCategory(Category $category, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['category' => $category]);

        return $this->render('product/products_by_category.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }

    #[Route('/addtocart/{idProduct}', name: 'app_addtocart', methods: ['GET'])]
    public function addtocart(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepo, CartRepository $cartRepo, ProductCartRepository $productCartRepository): Response
    {
        $user = $this->getUser();
        // si l'utilisateur est connecté
        if($user){
            $product = $productRepo->findOneBy([
                'id' => $request->get('idProduct')
            ]);
            $cart = $cartRepo->findOneBy([
                'user' => $user,
                'save' => false
            ]);
            // si l'utilisateur n'a pas de panier
            if(!$cart){
                $cart = new Cart();
                $cart->setUser($user);
                $cart->setTotal(0);
                $cart->setSave(false);
                $entityManager->persist($cart);
                $entityManager->flush();
            }

            $productCart = $productCartRepository->findOneBy([
                'cart' => $cart,
                'product' => $product
            ]);
            // si l'utilisateur n'a pas déjà ajouté ce produit à son panier
            if(!$productCart){
                $productCart = new ProductCart();
                $productCart->setProduct($product);
                $productCart->setQuantity(1);
                $productCart->setCart($cart);
                $cart->setTotal($product->getPriceHT());
                $entityManager->persist($productCart);
                $entityManager->flush();
            }
            //si il a deja ajouté lee produit dans son panier auparavant
            //recuperer la ligne et on ajoute 1 a la quantité
            else {
                $currentQuantity = $productCart->getQuantity();
                $productCart->setQuantity($currentQuantity + 1);
                $entityManager->persist($productCart);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_cart_show', ['id' => $cart->getId()]);
        }
        // si l'utilisateur n'est pas connecté
        else {
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/removefromcart/{productId}/{cartId}', name: 'app_removefromcart', methods: ['GET'])]
    public function removeFromCart(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepo, CartRepository $cartRepo, ProductCartRepository $productCartRepository){
        $product = $productRepo->findById($request->get('productId'));
        $cart = $cartRepo->findById($request->get('cartId'));
        $productCart = $productCartRepository->findOneBy([
            'product' => $product,
            'cart' => $cart
        ]);
        if ($product && $cart && $productCart) {
            $entityManager->remove($productCart);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_cart_show', ['id' => $request->get('cartId')]);
    }

}
