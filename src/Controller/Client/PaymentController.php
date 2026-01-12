<?php

namespace App\Controller\Client;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Reservation;

#[Route('/client/payment')]
#[IsGranted('ROLE_USER')]
class PaymentController extends AbstractController
{

    #[Route('/{room}', name: 'app_payment', methods: ['GET'], defaults: ['room' => null])]
    public function index(?int $room = null): Response
    {

        return $this->render('Client/payment/index.html.twig', [
            'roomId' => $room
        ]);
    }

    #[Route('/reservation/{id}', name: 'client_payment_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        if (!$user || $reservation->getUser()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('client/payment/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/payment/success', name: 'app_payment_success', methods: ['POST', 'GET'])]
    public function success(): Response
    {
        $this->addFlash('success', 'Thank you! Your luxury stay has been confirmed.');
        return $this->redirectToRoute('app_home');
    }
}
