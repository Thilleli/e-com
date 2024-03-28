<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeDashBordController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')){
            $this->addFlash('success', 'Vous êtes maintenant connecté !');
            return $this->render('admin/index.html.twig');
        }
        return $this->render('home_dash_bord/index.html.twig');
    }
}
