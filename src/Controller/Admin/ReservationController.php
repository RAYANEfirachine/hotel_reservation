<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/reservations')]
class ReservationController extends AbstractController
{
    #[Route('', name: 'admin_reservation_index')]
    #[Route('', name: 'app_reservation_index')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findAll();

        return $this->render('admin/reservations/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/{id}', name: 'admin_reservation_show')]
    public function show(Reservation $reservation): Response
    {
        return $this->render('admin/reservations/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }
}
