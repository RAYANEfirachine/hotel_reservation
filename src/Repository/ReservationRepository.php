<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function countOverlappingReservations($room, \DateTimeInterface $start, \DateTimeInterface $end): int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.room = :room')
            ->andWhere('r.status != :cancelled')
            ->andWhere('r.checkInDate < :end')
            ->andWhere('r.checkOutDate > :start')
            ->setParameter('room', $room)
            ->setParameter('cancelled', 'cancelled')
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
