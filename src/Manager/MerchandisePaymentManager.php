<?php

namespace App\Manager;

use App\Entity\Debt;
use App\Entity\Merchandise;
use App\Entity\MerchandisePayment;
use App\Entity\ProviderDebt;
use Doctrine\ORM\EntityManagerInterface;

class MerchandisePaymentManager
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create(Merchandise $merchandise)
    {
        $payment = new MerchandisePayment();
        $payment->setAmount($merchandise->getTotalEnterValue());

        if($merchandise->getPaidWith() === Merchandise::PAID_WITH_BILL) {
            $payment->bill();
        }

        $payment->setProvider($merchandise->getProvider());
        $payment->setDate($merchandise->getDate());

        $this->em->persist($payment);

        $this->em->flush();
    }

    public function createFromDebt(ProviderDebt $debt, float $amount, string $paymentType)
    {
        $payment = new MerchandisePayment();
        $payment->setAmount($amount);

        if($paymentType === Merchandise::PAID_WITH_BILL) {
            $payment->bill();
        }

        $payment->setProvider($debt->getProvider());
        $payment->setDate($debt->getDate());

        $this->em->persist($payment);

        $this->em->flush();
    }
}
