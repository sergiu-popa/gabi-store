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
    use IdTrait;
    use AmountTrait;
    use DateTrait;

    /**
     * @ORM\Column(type="smallint")
     */
    private $invoiceType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provider", inversedBy="merchandisePayments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $provider;

    public function getInvoiceType(): ?int
    {
        return $this->invoiceType;
    }

    public function setInvoiceType(int $invoiceType): self
    {
        $this->invoiceType = $invoiceType;

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
