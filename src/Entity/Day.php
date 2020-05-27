<?php

namespace App\Entity;

use App\Entity\Traits\AuthorTrait;
use App\Entity\Traits\DateTrait;
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
     * @Assert\PositiveOrZero()
     */
    private $bills_50_end;

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true, options={"unsigned":"true"})
     * @Assert\PositiveOrZero()
     */
    private $bills_100_end;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $confirmed;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="confirmed_by", referencedColumnName="id")
     */
    protected $confirmedBy;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="time_immutable", nullable=true)
     */
    private $endedAt;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Type("numeric")
     * @Assert\PositiveOrZero()
     */
    private $z;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $orderProviders = [];

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $paidProviders = [];

    public function __construct(UserInterface $author)
    {
        $this->date = new \DateTime();
        $this->startedAt = new \DateTimeImmutable();

        $this->author = $author;
        $this->bills_50_start = 1;
        $this->bills_100_start = 1;
        $this->confirmed = false;
    }

    public function isClosed(): bool
    {
        return $this->endedAt !== null;
    }

    public function isToday(): bool
    {
        return $this->date->format('Y-m-d') === (new \DateTime())->format('Y-m-d');
    }

    public function end(\DateTimeImmutable $date = null)
    {
        $this->endedAt = $date ?? new \DateTimeImmutable();
    }

    public function start(\DateTimeImmutable $date = null)
    {
        $this->startedAt = $date ?? new \DateTimeImmutable();
    }

    public function hasEnded(): bool
    {
        return $this->endedAt !== null;
    }

    public function getStartedAt(): \DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getEndedAt(): ?\DateTimeImmutable
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

    public function getZ(): ?float
    {
        return $this->z;
    }

    public function setZ(float $z): self
    {
        $this->z = $z;

        return $this;
    }

    public function getConfirmedBy(): ?UserInterface
    {
        return $this->confirmedBy;
    }

    public function setConfirmedBy(UserInterface $confirmedBy): void
    {
        $this->confirmedBy = $confirmedBy;
    }

    public function confirm(?UserInterface $user)
    {
        $this->confirmed = true;
        $this->confirmedBy = $user;
    }

    public function getOrderProviders(): ?array
    {
        return $this->orderProviders;
    }

    public function setOrderProviders(?array $orderProviders): self
    {
        $this->orderProviders = $orderProviders;

        return $this;
    }

    public function getPaidProviders(): ?array
    {
        return $this->paidProviders;
    }

    public function setPaidProviders(?array $paidProviders): self
    {
        $this->paidProviders = $paidProviders;

        return $this;
    }
}
