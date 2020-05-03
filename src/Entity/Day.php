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
     * @ORM\Column(type="time_immutable")
     */
    private $startedAt;

    /**
     * @var int
     * @ORM\Column(type="smallint", options={"unsigned":"true"})
     * @Assert\Positive()
     */
    private $bills_50_start;

    /**
     * @var int
     * @ORM\Column(type="smallint", options={"unsigned":"true"})
     * @Assert\Positive()
     */
    private $bills_100_start;

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true, options={"unsigned":"true"})
     * @Assert\Positive()
     */
    private $bills_50_end;

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true, options={"unsigned":"true"})
     * @Assert\Positive()
     */
    private $bills_100_end;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $confirmed;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="time_immutable", nullable=true)
     */
    private $endedAt;

    public function __construct(UserInterface $author)
    {
        $this->date = new \DateTime();
        $this->startedAt = new \DateTimeImmutable();

        $this->author = $author;
        $this->bills_50_start = 1;
        $this->bills_100_start = 1;
        $this->confirmed = true;
    }

    public function isToday(): bool
    {
        return $this->date === (new \DateTime())->setTime(0, 0, 0);
    }

    public function hasEnded(): bool
    {
        return $this->endedAt !== null;
    }

    public function getStartedAt(): \DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getEndedAt(): \DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function getBills50Start(): ?int
    {
        return $this->bills_50_start;
    }

    public function setBills50Start(int $bills_50_start): void
    {
        $this->bills_50_start = $bills_50_start;
    }

    public function getBills100Start(): ?int
    {
        return $this->bills_100_start;
    }

    public function setBills100Start(int $bills_100_start): void
    {
        $this->bills_100_start = $bills_100_start;
    }

    public function getBills50End(): ?int
    {
        return $this->bills_50_end;
    }

    public function setBills50End(int $bills_50_end): void
    {
        $this->bills_50_end = $bills_50_end;
    }

    public function getBills100End(): ?int
    {
        return $this->bills_100_end;
    }

    public function setBills100End(int $bills_100_end): void
    {
        $this->bills_100_end = $bills_100_end;
    }
}
