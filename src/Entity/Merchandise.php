<?php

namespace App\Entity;

use App\Entity\Traits\AmountTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\NameTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MerchandiseRepository")
 */
class Merchandise
{
    use IdTrait;
    use NameTrait;
    use DateTrait;
    use AmountTrait;

    public function __construct()
    {
        $this->merchandisePayments = new ArrayCollection();
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provider", inversedBy="merchandises")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provider;

    /**
     * @ORM\Column(type="float")
     */
    private $enterPrice;

    /**
     * @ORM\Column(type="float")
     */
    private $exitPrice;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MerchandisePayment", mappedBy="merchandise")
     */
    private $merchandisePayments;

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getEnterPrice(): ?float
    {
        return $this->enterPrice;
    }

    public function setEnterPrice(float $enterPrice): self
    {
        $this->enterPrice = $enterPrice;

        return $this;
    }

    public function getExitPrice(): ?float
    {
        return $this->exitPrice;
    }

    public function setExitPrice(float $exitPrice): self
    {
        $this->exitPrice = $exitPrice;

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
            $merchandisePayment->setMerchandise($this);
        }

        return $this;
    }

    public function removeMerchandisePayment(MerchandisePayment $merchandisePayment): self
    {
        if ($this->merchandisePayments->contains($merchandisePayment)) {
            $this->merchandisePayments->removeElement($merchandisePayment);
            // set the owning side to null (unless already changed)
            if ($merchandisePayment->getMerchandise() === $this) {
                $merchandisePayment->setMerchandise(null);
            }
        }

        return $this;
    }
}
