<?php

namespace App\Controller;

use App\Entity\Images;
use App\Form\ImagesType;
use App\Repository\ImagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin/images')]
class ImagesController extends AbstractController
{
    #[Route('/', name: 'app_images_index', methods: ['GET'])]
    public function index(ImagesRepository $imagesRepository): Response
    {
        return $this->render('images/index.html.twig', [
            'images' => $imagesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_images_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $image = new Images();
    $form = $this->createForm(ImagesType::class, $image);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupération du fichier téléchargé depuis le formulaire
        /** @var UploadedFile $file */
        $file = $form->get('file')->getData();

        if ($file) {
            // Définir le répertoire de destination
            $destination = $this->getParameter('kernel.project_dir') . '/public/img';

            // Utiliser le nom original du fichier
            $filename = $file->getClientOriginalName();

            // Déplacer le fichier vers le répertoire de destination
            try {
                $file->move($destination, $filename);

                // Mettre à jour le chemin du fichier dans l'entité
                $image->setPath("/ img/" . $filename);
            } catch (FileException $e) {
                // Gérer l'erreur en cas de problème de déplacement
                throw new \RuntimeException('Could not upload file: ' . $e->getMessage());
            }
        }

        // Persister l'entité et la sauvegarder dans la base de données
        $entityManager->persist($image);
        $entityManager->flush();

        // Rediriger vers l'index après avoir enregistré l'image
        return $this->redirectToRoute('app_images_index', [], Response::HTTP_SEE_OTHER);
    }

    // Afficher le formulaire pour créer une nouvelle image
    return $this->render('images/new.html.twig', [
        'image' => $image,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_images_show', methods: ['GET'])]
    public function show(Images $image): Response
    {
        return $this->render('images/show.html.twig', [
            'image' => $image,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_images_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Images $image, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ImagesType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_images_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('images/edit.html.twig', [
            'image' => $image,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_images_delete', methods: ['POST'])]
    public function delete(Request $request, Images $image, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_images_index', [], Response::HTTP_SEE_OTHER);
    }
}
