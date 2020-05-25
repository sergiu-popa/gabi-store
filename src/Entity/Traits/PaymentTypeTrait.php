<?php

namespace App\Entity\Traits;

use App\Entity\MerchandisePayment;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait PaymentTypeTrait
{
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     * @var int
     */
    protected $paymentType;

    public function invoice(): void
    {
        $this->paymentType = MerchandisePayment::TYPE_INVOICE;
    }

    public function paidWithInvoice(): bool
    {
        return $this->paymentType === MerchandisePayment::TYPE_INVOICE;
    }

    public function bill(): void
    {
        $this->paymentType = MerchandisePayment::TYPE_BILL;
    }

    public function paidWithBill(): bool
    {
        return $this->paymentType === MerchandisePayment::TYPE_BILL;
    }
    
    public function getPaymentTypeLabel(): string
    {
        return $this->paymentType === MerchandisePayment::TYPE_INVOICE ? 'facturi' : 'bonuri';
    }

    public function getPaymentType(): ?int
    {
        return $this->paymentType;
    }

    public function setPaymentType(int $paymentType): void
    {
        $this->paymentType = $paymentType;
    }
}
