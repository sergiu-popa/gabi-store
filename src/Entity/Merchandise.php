<?php

namespace App\Entity;

use App\Entity\Traits\AmountTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\DeletedAtTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\PaymentTypeTrait;
use App\Entity\Traits\SnapshotsTrait;
use App\Util\SnapshotableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MerchandiseRepository")
 */
class Merchandise implements \JsonSerializable, SnapshotableInterface
{
    use IdTrait;
    use NameTrait;
    use DateTrait;
    use AmountTrait;
    use PaymentTypeTrait;
    use DeletedAtTrait;
    use SnapshotsTrait;

    public function __construct($date = null, $provider = null)
    {
        $this->date = new \DateTime($date ?? 'now');

        if ($this->provider) {
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
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    private $enterPrice;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank()
     * @Assert\Positive()
     * @Assert\GreaterThan(propertyPath="enterPrice")
     */
    private $exitPrice;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MerchandiseCategory", inversedBy="merchandise")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $vat;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\Type("bool")
     * @var bool
     */
    private $isDebt;

    public function jsonSerialize()
    {
        return [
            'furnizor' => $this->provider->getName(),
            'categorie' => $this->category->getName(),
            'nume' => $this->name,
            'cantitate' => $this->amount,
            'pret intrare' => $this->enterPrice,
            'pret iesire' => $this->exitPrice,
            'TVA' => $this->vat,
            'datorie' => $this->isDebt() ? 'da' : 'nu',
            'plata' => $this->getPaymentTypeLabel(),
        ];
    }

    public function getTotalEnterValue()
    {
        return $this->amount * $this->enterPrice;
    }

    public function getTotalExitValue()
    {
        return $this->amount * $this->exitPrice;
    }

    public function getGrossProfitPercent()
    {
        return number_format(($this->getGrossProfit() / $this->enterPrice) * 100, 1);
    }

    public function getGrossProfit()
    {
        return $this->exitPrice - $this->enterPrice;
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

    public function getVat(): ?int
    {
        return $this->vat;
    }

    public function setVat(?int $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    public function isDebt(): ?bool
    {
        return $this->isDebt;
    }

    public function getIsDebt(): ?bool
    {
        return $this->isDebt;
    }

    public function setIsDebt(bool $isDebt): void
    {
        $this->isDebt = $isDebt;
    }
}
