<?php

namespace App\Entity;

use App\Entity\Traits\AmountTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\DeletedAtTrait;
use App\Entity\Traits\DeleteTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MerchandiseRepository")
 */
class Merchandise
{
    use IdTrait;
    use NameTrait;
    use DateTrait;
    use AmountTrait;
    use DeleteTrait;

    public function __construct($date = null, $provider = null)
    {
        $this->date = new \DateTime($date ?? 'now');

        if($this->provider) {
            $this->provider = $provider;
        }
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provider", inversedBy="merchandises")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provider;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive()
     */
    private $enterPrice;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive()
     * @Assert\GreaterThan(propertyPath="enterPrice")
     */
    private $exitPrice;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MerchandiseCategory", inversedBy="merchandise")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

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

    public function getCategory(): ?MerchandiseCategory
    {
        return $this->category;
    }

    public function setCategory(?MerchandiseCategory $category): self
    {
        $this->category = $category;

        return $this;
    }
}
