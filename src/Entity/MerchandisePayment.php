<?php

namespace App\Entity;

use App\Entity\Traits\AmountTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\DeletedAtTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\PaymentTypeTrait;
use App\Util\SnapshotableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MerchandisePaymentRepository")
 */
class MerchandisePayment implements \JsonSerializable, SnapshotableInterface
{
    public const TYPE_BILL = 1; // bonuri
    public const TYPE_INVOICE = 2; // facturi

    use IdTrait;
    use AmountTrait;
    use DateTrait;
    use DeletedAtTrait;
    use PaymentTypeTrait;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->invoice();
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provider", inversedBy="merchandisePayments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provider;

    public function jsonSerialize()
    {
        return [
            'furnizor' => $this->provider->getName(),
            'cantitate' => $this->amount,
            'data' => $this->date->format('Y-m-d'),
            'tip' => $this->paymentType === self::TYPE_BILL ? 'bon' : 'factura'
        ];
    }

    public function incrementAmount(float $amount): void
    {
        $this->amount += $amount;
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
}
