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
     * @ORM\ManyToOne(targetEntity="ProviderDebt", inversedBy="payments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $debt;

    public function partially()
    {
        $this->paidPartially = true;
    }

    public function jsonSerialize(): array
    {
        return [
            'cantitate' => $this->amount,
            'data' => $this->date->format('Y-m-d'),
            'platit partial' => $this->paidPartially,
            'datorie id' => $this->debt->getId()
        ];
    }

    public function getDebt(): ?ProviderDebt
    {
        return $this->debt;
    }

    public function setDebt(?ProviderDebt $debt): self
    {
        $this->debt = $debt;

        return $this;
    }
}
