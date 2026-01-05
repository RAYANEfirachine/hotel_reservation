<?php

namespace App\Controller\Client;

use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rooms')]
class RoomController extends AbstractController
{
    #[Route('/', name: 'client_room_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $rooms = $em->getRepository(Room::class)->findAll();

        return $this->render('client/room/index.html.twig', ['rooms' => $rooms]);
    }
}
