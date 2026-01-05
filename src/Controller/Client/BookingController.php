<?php

namespace App\Controller\Client;

use App\Entity\Reservation;
use App\Entity\User;
use App\Form\ReservationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/booking')]
class BookingController extends AbstractController
{
    #[Route('/new', name: 'client_booking_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $reservation = new Reservation();
        $form = $this->createForm(ReservationFormType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user) {
                throw $this->createAccessDeniedException();
            }

            $reservation->setUser($user);
            $em->persist($reservation);
            $em->flush();

            $this->addFlash('success', 'Reservation created.');

            return $this->redirectToRoute('client_booking_success');
        }

        return $this->render('client/booking/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/success', name: 'client_booking_success')]
    public function success(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        return $this->render('client/booking/success.html.twig');
    }

    #[Route('/{id}/cancel', name: 'client_booking_cancel', methods: ['POST'])]
    public function cancel(int $id, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $reservation = $em->getRepository(\App\Entity\Reservation::class)->find($id);
        if (!$reservation) {
            throw $this->createNotFoundException('Reservation not found');
        }

        $currentUser = $this->getUser();
        if (!$currentUser instanceof User || $reservation->getUser()->getId() !== $currentUser->getId()) {
            throw $this->createAccessDeniedException();
        }

        $reservation->setStatus('cancelled');
        $em->flush();

        $this->addFlash('success', 'Reservation cancelled.');
        return $this->redirectToRoute('client_booking_my_bookings');
    }

    #[Route('/my', name: 'client_booking_my_bookings')]
    public function myBookings(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $reservations = $em->getRepository(Reservation::class)->findBy(['user' => $user]);

        return $this->render('client/booking/my_bookings.html.twig', ['reservations' => $reservations]);
    }
}
