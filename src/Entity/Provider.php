<?php

namespace App\Entity;

use App\Entity\Traits\IdTrait;
use App\Entity\Traits\NameTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProviderRepository")
 */
class Provider
{
    use IdTrait;
    use NameTrait;

    public function __construct()
    {
        $this->debts = new ArrayCollection();
        $this->merchandises = new ArrayCollection();
        $this->merchandisePayments = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $cui;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $town;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $agent;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $mobileNumber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Debt", mappedBy="provider")
     */
    private $debts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Merchandise", mappedBy="provider")
     */
    private $merchandises;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MerchandisePayment", mappedBy="provider")
     */
    private $merchandisePayments;

    public function getCui(): ?string
    {
        return $this->cui;
    }

    public function setCui(string $cui): self
    {
        $this->cui = $cui;

        return $this;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(?string $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAgent(): ?string
    {
        return $this->agent;
    }

    public function setAgent(?string $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getMobileNumber(): ?string
    {
        return $this->mobileNumber;
    }

    public function setMobileNumber(?string $mobileNumber): self
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    /**
     * @return Collection|Debt[]
     */
    public function getDebts(): Collection
    {
        return $this->debts;
    }

    public function addDebt(Debt $debt): self
    {
        if (!$this->debts->contains($debt)) {
            $this->debts[] = $debt;
            $debt->setProvider($this);
        }

        return $this;
    }

    public function removeDebt(Debt $debt): self
    {
        if ($this->debts->contains($debt)) {
            $this->debts->removeElement($debt);
            // set the owning side to null (unless already changed)
            if ($debt->getProvider() === $this) {
                $debt->setProvider(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Merchandise[]
     */
    public function getMerchandises(): Collection
    {
        return $this->merchandises;
    }

    public function addMerchandise(Merchandise $merchandise): self
    {
        if (!$this->merchandises->contains($merchandise)) {
            $this->merchandises[] = $merchandise;
            $merchandise->setProvider($this);
        }

        return $this;
    }

    public function removeMerchandise(Merchandise $merchandise): self
    {
        if ($this->merchandises->contains($merchandise)) {
            $this->merchandises->removeElement($merchandise);
            // set the owning side to null (unless already changed)
            if ($merchandise->getProvider() === $this) {
                $merchandise->setProvider(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MerchandisePayment[]
     */
    public function getMerchandisePayments(): Collection
    {
        return $this->merchandisePayments;
    }

    public function addMerchandisePayment(MerchandisePayment $merchandisePayment): self
    {
        if (!$this->merchandisePayments->contains($merchandisePayment)) {
            $this->merchandisePayments[] = $merchandisePayment;
            $merchandisePayment->setProvider($this);
        }

        return $this;
    }

    public function removeMerchandisePayment(MerchandisePayment $merchandisePayment): self
    {
        if ($this->merchandisePayments->contains($merchandisePayment)) {
            $this->merchandisePayments->removeElement($merchandisePayment);
            // set the owning side to null (unless already changed)
            if ($merchandisePayment->getProvider() === $this) {
                $merchandisePayment->setProvider(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
