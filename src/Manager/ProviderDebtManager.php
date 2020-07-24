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

    public function create(Merchandise $merchandise)
    {
        $debt = new ProviderDebt();
        $debt->setAmount($merchandise->getTotalEnterValue());
        $debt->setProvider($merchandise->getProvider());
        $debt->setDate($merchandise->getDate());

        $this->em->persist($debt);

        $this->em->flush();
    }

    public function update(int $previousTotalEnterValue, Merchandise $merchandise)
    {
        $debt = $this->debtRepository->findForDateAndProvider($merchandise);
        $difference = abs($merchandise->getTotalEnterValue() - $previousTotalEnterValue);

        if($difference !== 0) {
            if ($merchandise->getTotalEnterValue() > $previousTotalEnterValue) {
                $debt->incrementAmount($difference);
            } else {
                $debt->decrementAmount($difference);
            }

            $this->updateOrDeleteDebt($debt);
        }
    }

    public function delete(Merchandise $merchandise)
    {
        $debt = $this->debtRepository->findForDateAndProvider($merchandise);

        $debt->decrementAmount($merchandise->getTotalEnterValue());

        $this->updateOrDeleteDebt($debt);
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

    private function updateOrDeleteDebt(?ProviderDebt $debt): void
    {
        if ($debt->getAmount() === 0.0) {
            $debt->delete();
        } else {
            $debt->update();
        }

        $this->em->flush();
    }
}
