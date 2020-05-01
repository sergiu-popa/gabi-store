<?php

namespace App\Entity;

use App\Entity\Traits\AuthorTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\DeletedAtTrait;
use App\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DayRepository")
 */
class Day
{
    use IdTrait;
    use DateTrait;
    use AuthorTrait;

    /**
     * @ORM\Column(type="date", unique=true)
     * @Assert\Type("\DateTimeInterface")
     */
    private $date;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime_immutable")
     */
    private $startedAt;

    /**
     * @var int
     * @ORM\Column(type="smallint", options={"unsigned":"true"})
     * @Assert\Positive()
     */
    private $bills_50;

    /**
     * @var int
     * @ORM\Column(type="smallint", options={"unsigned":"true"})
     * @Assert\Positive()
     */
    private $bills_100;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $confirmed;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $endedAt;

    public function __construct(UserInterface $author)
    {
        $this->date = new \DateTime();
        $this->startedAt = new \DateTimeImmutable();

        $this->author = $author;
        $this->bills_50 = 1;
        $this->bills_100 = 1;
        $this->confirmed = false;
    }

    public function getStartedAt(): \DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getEndedAt(): \DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function getBills50(): ?int
    {
        return $this->bills_50;
    }

    /**
     * @param int $bills_50
     */
    public function setBills50(int $bills_50): void
    {
        $this->bills_50 = $bills_50;
    }

    public function getBills100(): ?int
    {
        return $this->bills_100;
    }

    public function setBills100(int $bills_100): void
    {
        $this->bills_100 = $bills_100;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }
}
