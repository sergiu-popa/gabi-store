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
     * @ORM\ManyToOne(targetEntity="App\Entity\Merchandise", inversedBy="merchandisePayments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $merchandise;

    /**
     * @ORM\Column(type="smallint")
     */
    private $invoiceType;

    public function getMerchandise(): ?Merchandise
    {
        return $this->merchandise;
    }

    public function setMerchandise(?Merchandise $merchandise): self
    {
        $this->merchandise = $merchandise;

        return $this;
    }

    public function getInvoiceType(): ?int
    {
        return $this->invoiceType;
    }

    public function setInvoiceType(int $invoiceType): self
    {
        $this->invoiceType = $invoiceType;

        return $this;
    }
}
