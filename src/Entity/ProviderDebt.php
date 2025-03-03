<?php

namespace App\Entity;

use App\Entity\Traits\AmountTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\DeletedAtTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\PaidPartiallyTrait;
use App\Entity\Traits\PaymentTypeTrait;
use App\Entity\Traits\UpdatedAtTrait;
use App\Util\SnapshotableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProviderDebtRepository")
 */
class ProviderDebt implements \JsonSerializable, SnapshotableInterface
{
    use IdTrait;
    use AmountTrait;
    use DateTrait;
    use PaidPartiallyTrait;
    use PaymentTypeTrait;
    use DeletedAtTrait;
    use UpdatedAtTrait;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
        $this->date = new \DateTime();
        $this->paidFully = false;
        $this->paidPartially = false;
        $this->paymentType = MerchandisePayment::TYPE_INVOICE;
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provider", inversedBy="debts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provider;

    /**
     * @ORM\Column(type="boolean")
     */
    private $paidFully;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DebtPayment", mappedBy="debt")
     * @var DebtPayment[]
     */
    private $payments;

    public function payFully()
    {
        $this->paidFully = true;
        $this->paidPartially = false;
    }

    public function payPartially(float $paidAmount)
    {
        $this->amount = $this->amount - $paidAmount;

        $this->paidPartially = true;
        $this->paidFully = false;
    }

    public function getTotalPaid(): float
    {
        $total = 0;

        foreach ($this->payments as $payment) {
            if(! $payment->isDeleted()) {
                $total += $payment->getAmount();
            }
        }

        return $total;
    }

    public function jsonSerialize(): array
    {
        return [
            'furnizor' => $this->provider->getName(),
            'cantitate' => $this->amount,
            'data' => $this->date->format('Y-m-d'),
            'plata tip' => $this->getPaymentTypeLabel(),
            'platit complet' => $this->paidFully,
            'platit partial' => $this->paidPartially,
            'plati' => $this->payments->count()
        ];
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getPaidFully(): ?bool
    {
        return $this->paidFully;
    }

    public function setPaidFully(bool $paidFully): self
    {
        $this->paidFully = $paidFully;

        return $this;
    }

    /**
     * @return Collection|DebtPayment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addDebtPayment(DebtPayment $debtPayment): self
    {
        if (!$this->payments->contains($debtPayment)) {
            $this->payments[] = $debtPayment;
            $debtPayment->setDebt($this);
        }

        return $this;
    }

    public function removeDebtPayment(DebtPayment $debtPayment): self
    {
        if ($this->payments->contains($debtPayment)) {
            $this->payments->removeElement($debtPayment);
            // set the owning side to null (unless already changed)
            if ($debtPayment->getDebt() === $this) {
                $debtPayment->setDebt(null);
            }
        }

        return $this;
    }
}
