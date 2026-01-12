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
use Symfony\Component\Form\FormError;

#[Route('/booking')]
class BookingController extends AbstractController
{
    #[Route('/new', name: 'client_booking_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $reservation = new Reservation();
        $form = $this->createForm(ReservationFormType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user) {
                throw $this->createAccessDeniedException();
            }


            $checkIn = $reservation->getCheckInDate();
            $checkOut = $reservation->getCheckOutDate();
            if ($checkOut <= $checkIn) {
                $form->addError(new FormError('Check-out date must be after check-in date.'));
            } else {
                $interval = $checkIn->diff($checkOut);
                $days = (int) $interval->days;
                if ($days <= 0) {
                    $form->addError(new FormError('Check-out date must be after check-in date.'));
                } else {
                    $nights = max(1, $days);
                    $roomType = $reservation->getRoom()->getRoomType();
                    $pricePerDay = (float) $roomType->getPricePerDay();
                    $total = $nights * $pricePerDay;
                    if ($total <= 0) {
                        throw new \LogicException('Calculated reservation total must be greater than 0.');
                    }
                    $reservation->setTotalPrice(number_format($total, 2, '.', ''));


                    $overlapCount = $em->getRepository(Reservation::class)
                        ->countOverlappingReservations(
                            $reservation->getRoom(),
                            $reservation->getCheckInDate(),
                            $reservation->getCheckOutDate()
                        );

                    if ($overlapCount > 0) {
                        $this->addFlash('error', 'This room is already booked for the selected dates.');
                        $roomId = $reservation->getRoom() ? $reservation->getRoom()->getId() : 'n/a';
                        $checkIn = $reservation->getCheckInDate() ? $reservation->getCheckInDate()->format('Y-m-d') : '-';
                        $checkOut = $reservation->getCheckOutDate() ? $reservation->getCheckOutDate()->format('Y-m-d') : '-';
                        $this->addFlash('info', sprintf('Overlap debug: count=%d, room=%s, checkIn=%s, checkOut=%s', $overlapCount, $roomId, $checkIn, $checkOut));

                        return $this->render('client/booking/new.html.twig', [
                            'form' => $form->createView(),
                            'room_is_full' => true,
                        ]);
                    }

                    $reservation->setUser($user);
                    $em->persist($reservation);
                    $em->flush();

                    $this->addFlash('success', 'Reservation created.');

                    return $this->redirectToRoute('client_booking_success');
                }
            }
        }

        return $this->render('client/booking/new.html.twig', ['form' => $form->createView(), 'room_is_full' => false]);
    }

    #[Route('/success', name: 'client_booking_success')]
    public function success(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('client/booking/success.html.twig');
    }

    #[Route('/{id}/cancel', name: 'client_booking_cancel', methods: ['POST'])]
    public function cancel(int $id, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

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
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $reservations = $em->getRepository(Reservation::class)->findBy(['user' => $user]);

        return $this->render('client/booking/my_bookings.html.twig', ['reservations' => $reservations]);
    }
}
