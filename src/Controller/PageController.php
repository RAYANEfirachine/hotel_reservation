<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{

    #[Route('/help', name: 'app_help')]
    public function help(): Response
    {
        return $this->render('pages/help.html.twig');
    }


    #[Route('/privacy', name: 'app_privacy')]
    public function privacy(): Response
    {
        return $this->render('pages/privacy.html.twig');
    }
    #[Route('/terms', name: 'app_terms')]
    public function terms(): Response
    {
        return $this->render('pages/terms.html.twig');
    }
}
