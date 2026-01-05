<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $roomNumber;

    #[ORM\Column(type: 'string', length: 50)]
    private string $status = 'available';

    #[ORM\ManyToOne(targetEntity: RoomType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private RoomType $roomType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoomNumber(): string
    {
        return $this->roomNumber;
    }

    public function setRoomNumber(string $roomNumber): self
    {
        $this->roomNumber = $roomNumber;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRoomType(): RoomType
    {
        return $this->roomType;
    }

    public function setRoomType(RoomType $roomType): self
    {
        $this->roomType = $roomType;

        return $this;
    }
}
