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

    public function create(Merchandise $merchandise)
    {
        $payment = new MerchandisePayment();
        $payment->setAmount($merchandise->getTotalEnterValue());

        if ($merchandise->paidWithBill()) {
            $payment->bill();
        }

        $payment->setProvider($merchandise->getProvider());
        $payment->setDate($merchandise->getDate());

        $this->em->persist($payment);

        $this->em->flush();
    }

    public function update(int $previousTotalEnterValue, Merchandise $merchandise)
    {
        $payment = $this->repository->findForDateAndProvider($merchandise);

        $difference = abs($merchandise->getTotalEnterValue() - $previousTotalEnterValue);

        if($difference !== 0) {
            if ($merchandise->getTotalEnterValue() > $previousTotalEnterValue) {
                $payment->incrementAmount($difference);
            } else {
                $payment->decrementAmount($difference);
            }

            $this->updateOrDeletePayment($payment);
        }
    }

    public function delete(Merchandise $merchandise)
    {
        $debt = $this->repository->findForDateAndProvider($merchandise);

        $debt->decrementAmount($merchandise->getTotalEnterValue());

        $this->updateOrDeletePayment($debt);
    }

    private function updateOrDeletePayment(?MerchandisePayment $payment): void
    {
        if ($payment->getAmount() === 0.0) {
            $payment->delete();
        } else {
            $payment->update();
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
        $payment->setDate(new \DateTime());

        $this->em->persist($payment);

        $this->em->flush();
    }
}
