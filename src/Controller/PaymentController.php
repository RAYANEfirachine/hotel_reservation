<?php

namespace App\Controller;

use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    #[Route('/payment/success/{id}', name: 'payment_success', methods: ['GET','POST'])]
    public function success(int $id, EntityManagerInterface $em): RedirectResponse
    {
        $reservation = $em->getRepository(Reservation::class)->find($id);

        if (!$reservation) {
            $this->addFlash('error', 'Reservation not found.');
            return $this->redirectToRoute('app_reservation_index');
        }

        $reservation->setStatus('completed');
        $em->flush();

        $this->addFlash('success', 'Payment successful — your reservation is confirmed.');

        return $this->redirectToRoute('app_reservation_index');
    }
}
