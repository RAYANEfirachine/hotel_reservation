<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            
            $this->addFlash('success', 'Your message has been sent successfully! Our concierge will contact you soon.');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('pages/contact.html.twig');
    }
}
