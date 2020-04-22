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

    public function getTotalEnterValue()
    {
        return $this->amount * $this->enterPrice;
    }

    public function getTotalExitValue()
    {
        return $this->amount * $this->exitPrice;
    }

    public function getGrossProfit()
    {
        return $this->exitPrice - $this->enterPrice;
    }

    // TODO getGrossProfitPercentage

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
}
