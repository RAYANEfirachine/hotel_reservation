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
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/admin/rooms')]
#[IsGranted('ROLE_ADMIN')]
class RoomController extends AbstractController
{
    #[Route('/', name: 'admin_room_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $rooms = $em->getRepository(Room::class)->findAll();
        return $this->render('admin/room/index.html.twig', ['rooms' => $rooms]);
    }

    #[Route('/new', name: 'admin_room_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomFormType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = $this->uploadImage($imageFile, $slugger);
                $room->setImage($newFilename);
            }

            $em->persist($room);
            $em->flush();

            $this->addFlash('success', 'Room created successfully.');
            return $this->redirectToRoute('admin_room_index');
        }

        return $this->render('admin/room/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_room_edit', methods: ['GET', 'POST'])]
    public function edit(Room $room, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(RoomFormType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // 1. حذف الصورة القديمة من المجلد لتوفير المساحة
                if ($room->getImage()) {
                    $oldPath = $this->getParameter('rooms_images_directory').'/'.$room->getImage();
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                // 2. رفع الصورة الجديدة وتحديث قاعدة البيانات
                $newFilename = $this->uploadImage($imageFile, $slugger);
                $room->setImage($newFilename);
            }

            $em->flush();
            $this->addFlash('success', 'Room updated successfully.');
            return $this->redirectToRoute('admin_room_index');
        }

        return $this->render('admin/room/edit.html.twig', [
            'form' => $form->createView(),
            'room' => $room
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_room_delete', methods: ['POST'])]
    public function delete(Request $request, Room $room, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            // حذف الملف الفعلي من السيرفر عند حذف السجل
            if ($room->getImage()) {
                $imagePath = $this->getParameter('rooms_images_directory').'/'.$room->getImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $em->remove($room);
            $em->flush();
            $this->addFlash('success', 'Room deleted successfully.');
        }

        return $this->redirectToRoute('admin_room_index');
    }

    /**
     * دالة مساعدة لمعالجة رفع الصور وتوليد اسم آمن
     */
    private function uploadImage(UploadedFile $imageFile, SluggerInterface $slugger): string
    {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

        try {
            $imageFile->move(
                $this->getParameter('rooms_images_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            $this->addFlash('error', 'Failed to upload image.');
        }

        return $newFilename;
    }
}
