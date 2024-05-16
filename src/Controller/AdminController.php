<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ImagesManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
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

    #[Route('/upload', name: 'app_admin_upload')]
    public function upload(Request $request, ImagesManager $imageManager, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $file = new File();

        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $uploadedFile = $form->get('file')->getData();
            $fileName = $imageManager->upload($uploadedFile, $file->isPublic());

            $file->setPath($fileName);
            $file->setType('image');

            $entityManager->persist($file);
            $entityManager->flush();
        }
        return $this->render('admin/upload.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/download', name: 'app_admin_download')]
    public function download(EntityManagerInterface $em)
    {
        $images = $em->getRepository(File::class)->findAll();
        return $this->render("admin/download.html.twig", [
            'images' => $images
        ]);

    }

    #[Route('/image/stream/{id}', name: 'app_image_stream')]
    public function imageStream(int $id, EntityManagerInterface $em, ImagesManager $imageManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $image = $em->getRepository(File::class)->find($id);
        $filePath = $image->getPath();

        //return new Response('Fichier ok derrier image->path');

        return $imageManager->stream($filePath);
    }
}
