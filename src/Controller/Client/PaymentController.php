<?php

namespace App\Controller\Client;

use App\Entity\Payment;
use App\Entity\Reservation;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/client/payment')]
class PaymentController extends AbstractController
{
    #[Route('/{id}', name: 'client_payment_show', methods: ['GET','POST'])]
    public function show(Reservation $reservation, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user || $user->getId() !== $reservation->getUser()->getId()) {
            throw new AccessDeniedException();
        }

        if ($request->isMethod('POST')) {
            $payment = new Payment();
            $payment->setAmount($reservation->getTotalPrice());
            $payment->setPaymentMethod('card');
            $payment->setPaymentStatus('paid');
            $payment->setPaymentDate(new \DateTimeImmutable());
            $payment->setReservation($reservation);

            $reservation->setStatus('confirmed');

            $em->persist($payment);
            $em->persist($reservation);
            $em->flush();

            return $this->redirectToRoute('client_payment_success', ['id' => $reservation->getId()]);
        }

        return $this->render('client/payment/show.html.twig', ['reservation' => $reservation]);
    }

    #[Route('/{id}/success', name: 'client_payment_success', methods: ['GET'])]
    public function success(Reservation $reservation): Response
    {
        $user = $this->getUser();
        if (!$user || $user->getId() !== $reservation->getUser()->getId()) {
            throw new AccessDeniedException();
        }

        return $this->render('client/payment/success.html.twig', ['reservation' => $reservation]);
    }
}
