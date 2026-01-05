<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/reservation')]
#[IsGranted('ROLE_ADMIN')]
class ReservationController extends AbstractController
{
    #[Route('', name: 'admin_reservation_index')]
    #[Route('/', name: 'admin_reservation_index_slash')]
    public function index(EntityManagerInterface $em): Response
    {
        // placeholder - list reservations when available
        return $this->render('admin/reservation/index.html.twig');
    }
}
