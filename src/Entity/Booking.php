<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Services::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Services $service = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $startTime = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $endTime = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isReserved = false;

    #[ORM\Column(type: 'integer')]
    private int $dayOfWeek; // Valeur entre 1 (lundi) et 7 (dimanche)

    // Constructor
    public function __construct()
    {
        $this->isReserved = false; // Par défaut, le créneau est libre
    }

    // Getters et Setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getService(): ?Services
    {
        return $this->service;
    }

    public function setService(Services $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function isReserved(): bool
    {
        return $this->isReserved;
    }

    public function setReserved(bool $isReserved): self
    {
        $this->isReserved = $isReserved;

        return $this;
    }

    public function getDayOfWeek(): int
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(int $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }
}
