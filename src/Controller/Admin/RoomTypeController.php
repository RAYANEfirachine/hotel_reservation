<?php

namespace App\Controller\Admin;

use App\Entity\RoomType;
use App\Form\RoomTypeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/room-types')]
#[IsGranted('ROLE_ADMIN')]
class RoomTypeController extends AbstractController
{
    #[Route('/', name: 'admin_room_type_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $types = $em->getRepository(RoomType::class)->findAll();

        return $this->render('admin/room_type/index.html.twig', ['types' => $types]);
    }

    #[Route('/new', name: 'admin_room_type_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $type = new RoomType();
        $form = $this->createForm(RoomTypeFormType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($type);
            $em->flush();

            $this->addFlash('success', 'Room type saved.');

            return $this->redirectToRoute('admin_room_type_index');
        }

        return $this->render('admin/room_type/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/edit', name: 'admin_room_type_edit')]
    public function edit(RoomType $type, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RoomTypeFormType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Room type updated.');

            return $this->redirectToRoute('admin_room_type_index');
        }

        return $this->render('admin/room_type/edit.html.twig', ['form' => $form->createView(), 'type' => $type]);
    }

    #[Route('/{id}/delete', name: 'admin_room_type_delete', methods: ['POST'])]
    public function delete(RoomType $type, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$type->getId(), $request->request->get('_token'))) {
            $em->remove($type);
            $em->flush();
            $this->addFlash('success', 'Room type deleted.');
        }

        return $this->redirectToRoute('admin_room_type_index');
    }
}
