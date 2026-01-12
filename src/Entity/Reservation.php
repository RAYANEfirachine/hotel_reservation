<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $checkInDate = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $checkOutDate = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $status = 'pending';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $totalPrice = '0.00';

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // تعديل العلاقة هنا لترتبط بـ inversedBy الموجود في كلاس Room
    #[ORM\ManyToOne(targetEntity: Room::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $room = null;

    public function getId(): ?int { return $this->id; }

    public function getCheckInDate(): ?\DateTimeInterface { return $this->checkInDate; }

    public function setCheckInDate(?\DateTimeInterface $checkInDate): self
    {
        $this->checkInDate = $checkInDate;
        return $this;
    }

    public function getCheckOutDate(): ?\DateTimeInterface { return $this->checkOutDate; }

    public function setCheckOutDate(?\DateTimeInterface $checkOutDate): self
    {
        $this->checkOutDate = $checkOutDate;
        return $this;
    }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getTotalPrice(): string { return $this->totalPrice; }
    public function setTotalPrice(string $totalPrice): self { $this->totalPrice = $totalPrice; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }

    public function getRoom(): ?Room { return $this->room; }
    public function setRoom(?Room $room): self { $this->room = $room; return $this; }
}
