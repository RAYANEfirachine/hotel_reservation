<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReservationService
{
    public function __construct(private ReservationRepository $repo, private EntityManagerInterface $em)
    {
    }

    public function createReservation(Reservation $reservation): Reservation
    {
        $this->em->persist($reservation);
        $this->em->flush();

        return $reservation;
    }

    // TODO: add availability checks, pricing, cancellation logic
}
