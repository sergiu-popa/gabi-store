<?php

namespace App\Manager;

use App\Entity\DebtPayment;
use App\Entity\Merchandise;
use App\Entity\ProviderDebt;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProviderDebtManager
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ProviderRepository */
    private $providerRepository;

    public function __construct(EntityManagerInterface $em, ProviderRepository $providerRepository)
    {
        $this->em = $em;
        $this->providerRepository = $providerRepository;
    }

    public function create(Merchandise $merchandise)
    {
        $debt = new ProviderDebt();
        $debt->setAmount($merchandise->getTotalEnterValue());
        $debt->setProvider($merchandise->getProvider());
        $debt->setMerchandise($merchandise);
        $debt->setDate($merchandise->getDate());

        $this->em->persist($debt);

        $this->em->flush();
    }

    public function findUnpaid()
    {
        return $this->providerRepository->findUnpaid();
    }

    public function pay(ProviderDebt $debt, string $type, float $amount)
    {
        $debt->update();

        if ($type === 'fully' || $amount === $debt->getRemainingAmount()) {
            $this->payFully($debt);
        } else {
            $this->payPartially($debt, $amount);
        }
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
        $debt->payPartially();

        $payment = new DebtPayment();
        $payment->setDebt($debt);
        $payment->setAmount($amount);
        $payment->partially();

        $this->em->persist($payment);
        $this->em->flush();
    }
}
