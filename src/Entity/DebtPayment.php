<?php

namespace App\Entity;

use App\Entity\Traits\AmountTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\DeletedAtTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\PaidPartiallyTrait;
use App\Util\SnapshotableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DebtPaymentRepository")
 */
class DebtPayment implements \JsonSerializable, SnapshotableInterface
{
    use IdTrait;
    use AmountTrait;
    use DateTrait;
    use PaidPartiallyTrait;
    use DeletedAtTrait;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->paidPartially = false;
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Debt", inversedBy="debtPayments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $debt;

    public function jsonSerialize()
    {
        return [
            'cantitate' => $this->amount,
            'data' => $this->date->format('Y-m-d'),
            'platit partial' => $this->paidPartially,
            'datorie' => $this->debt->getId()
        ];
    }

    public function getDebt(): ?Debt
    {
        return $this->debt;
    }

    public function setDebt(?Debt $debt): self
    {
        $this->debt = $debt;

        return $this;
    }
}
