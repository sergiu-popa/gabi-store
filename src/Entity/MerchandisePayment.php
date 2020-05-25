<?php

namespace App\Entity;

use App\Entity\Traits\AmountTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\DeletedAtTrait;
use App\Entity\Traits\IdTrait;
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

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->invoice();
    }

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

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
            'tip' => $this->type === self::TYPE_BILL ? 'bon' : 'factura'
        ];
    }

    public function invoice(): void
    {
        $this->type = self::TYPE_INVOICE;
    }

    public function paidWithInvoice(): bool
    {
        return $this->type === self::TYPE_INVOICE;
    }

    public function bill(): void
    {
        $this->type = self::TYPE_BILL;
    }

    public function paidWithBill(): bool
    {
        return $this->type === self::TYPE_BILL;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function getTypeLabel(): string
    {
        return $this->type === 1 ? 'bonuri' : 'facturi';
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
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
