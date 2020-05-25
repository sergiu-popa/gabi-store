<?php

namespace App\Manager;

use App\Entity\DebtPayment;
use App\Entity\Merchandise;
use App\Entity\ProviderDebt;
use App\Repository\ProviderDebtRepository;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProviderDebtManager
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ProviderRepository */
    private $providerRepository;

    /** @var MerchandisePaymentManager */
    private $paymentManager;

    /** @var ProviderDebtRepository */
    private $debtRepository;

    public function __construct(
        EntityManagerInterface $em,
        ProviderRepository $providerRepository,
        MerchandisePaymentManager $paymentManager,
        ProviderDebtRepository $debtRepository
    ) {
        $this->em = $em;
        $this->providerRepository = $providerRepository;
        $this->paymentManager = $paymentManager;
        $this->debtRepository = $debtRepository;
    }

    public function update(Merchandise $merchandise)
    {
        $debt = $this->debtRepository->findTodayForProvider($merchandise->getProvider());

        if ($debt) {
            $debt->incrementAmount($merchandise->getTotalEnterValue());
            $debt->update();
        } else {
            $debt = new ProviderDebt();
            $debt->setAmount($merchandise->getTotalEnterValue());
            $debt->setProvider($merchandise->getProvider());
            $debt->setDate($merchandise->getDate());

            $this->em->persist($debt);
        }

        $this->em->flush();
    }

    public function findUnpaid()
    {
        return $this->providerRepository->findUnpaid();
    }

    public function findUnpaidTotalAmount(): float
    {
        return $this->providerRepository->findUnpaidTotalAmount();
    }

    public function pay(ProviderDebt $debt, string $type, float $amount)
    {
        $debt->update();

        if ($type === 'fully' || $amount === $debt->getAmount()) {
            $this->payFully($debt);
        } else {
            $this->payPartially($debt, $amount);
        }

        $this->paymentManager->createFromDebt($debt, $amount);
    }

    private function payFully(ProviderDebt $debt)
    {
        $debt->payFully();

        $payment = new DebtPayment();
        $payment->setDebt($debt);
        $payment->setAmount($debt->getAmount());

        $this->em->persist($payment);
        $this->em->flush();
    }

    private function payPartially(ProviderDebt $debt, float $amount)
    {
        $debt->payPartially($amount);

        $payment = new DebtPayment();
        $payment->setDebt($debt);
        $payment->setAmount($amount);
        $payment->partially();

        $this->em->persist($payment);
        $this->em->flush();
    }
}
