<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use App\Form\RoomFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/rooms')]
#[IsGranted('ROLE_ADMIN')]
class RoomController extends AbstractController
{
    #[Route('/', name: 'admin_room_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $rooms = $em->getRepository(Room::class)->findAll();

        return $this->render('admin/room/index.html.twig', ['rooms' => $rooms]);
    }

    #[Route('/new', name: 'admin_room_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomFormType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($room);
            $em->flush();

            $this->addFlash('success', 'Room created.');

            return $this->redirectToRoute('admin_room_index');
        }

        return $this->render('admin/room/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/edit', name: 'admin_room_edit')]
    public function edit(Room $room, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RoomFormType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Room updated.');

            return $this->redirectToRoute('admin_room_index');
        }

        return $this->render('admin/room/edit.html.twig', ['form' => $form->createView(), 'room' => $room]);
    }

    #[Route('/{id}/delete', name: 'admin_room_delete', methods: ['POST'])]
    public function delete(Room $room, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $em->remove($room);
            $em->flush();
            $this->addFlash('success', 'Room deleted.');
        }

        return $this->redirectToRoute('admin_room_index');
    }
}
