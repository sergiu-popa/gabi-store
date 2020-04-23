<?php

namespace App\Entity;

use App\Entity\Traits\AmountTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MerchandisePaymentRepository")
 */
class MerchandisePayment
{
    public const TYPE_BILL = 1;
    public const TYPE_INVOICE = 2;

    use IdTrait;
    use AmountTrait;
    use DateTrait;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provider", inversedBy="merchandisePayments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provider;

    public function withInvoice()
    {
        return $this->type === self::TYPE_INVOICE;
    }

    public function withBill()
    {
        return $this->type === self::TYPE_BILL;
    }

    public function getType(): ?int
    {
        return $this->type;
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
