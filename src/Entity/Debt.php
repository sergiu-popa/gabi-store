<?php

namespace App\Entity;

use App\Entity\Traits\AmountTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\DeleteTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\PaidPartiallyTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DebtRepository")
 */
class Debt
{
    use IdTrait;
    use AmountTrait;
    use DateTrait;
    use PaidPartiallyTrait;
    use DeleteTrait;

    public function __construct()
    {
        $this->debtPayments = new ArrayCollection();
        $this->date = new \DateTime();
        $this->paidFully = false;
        $this->paidPartially = false;
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
     */
    private $debtPayments;

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
    public function getDebtPayments(): Collection
    {
        return $this->debtPayments;
    }

    public function addDebtPayment(DebtPayment $debtPayment): self
    {
        if (!$this->debtPayments->contains($debtPayment)) {
            $this->debtPayments[] = $debtPayment;
            $debtPayment->setDebt($this);
        }

        return $this;
    }

    public function removeDebtPayment(DebtPayment $debtPayment): self
    {
        if ($this->debtPayments->contains($debtPayment)) {
            $this->debtPayments->removeElement($debtPayment);
            // set the owning side to null (unless already changed)
            if ($debtPayment->getDebt() === $this) {
                $debtPayment->setDebt(null);
            }
        }

        return $this;
    }
}
