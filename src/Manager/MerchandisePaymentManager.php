<?php

namespace App\Manager;

use App\Entity\Merchandise;
use App\Entity\MerchandisePayment;
use App\Entity\ProviderDebt;
use App\Repository\MerchandisePaymentRepository;
use Doctrine\ORM\EntityManagerInterface;

class MerchandisePaymentManager
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var MerchandisePaymentRepository */
    private $repository;

    public function __construct(EntityManagerInterface $em, MerchandisePaymentRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    public function update(Merchandise $merchandise)
    {
        $payment = $this->repository->findTodayForProvider($merchandise->getProvider(), $merchandise->getPaymentType());

        if($payment) {
            $payment->incrementAmount($merchandise->getTotalEnterValue());
        } else {
            $payment = new MerchandisePayment();
            $payment->setAmount($merchandise->getTotalEnterValue());

            if ($merchandise->paidWithBill()) {
                $payment->bill();
            }

            $payment->setProvider($merchandise->getProvider());
            $payment->setDate($merchandise->getDate());

            $this->em->persist($payment);
        }

        $this->em->flush();
    }

    public function createFromDebt(ProviderDebt $debt, float $amount)
    {
        $payment = new MerchandisePayment();
        $payment->setAmount($amount);

        if($debt->getPaymentType() === MerchandisePayment::TYPE_BILL) {
            $payment->bill();
        }

        $payment->setPaymentType($debt->getPaymentType());
        $payment->setProvider($debt->getProvider());
        $payment->setDate($debt->getDate());

        $this->em->persist($payment);

        $this->em->flush();
    }
}
