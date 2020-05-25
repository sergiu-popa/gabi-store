<?php

namespace App\Manager;

use App\Entity\Merchandise;
use App\Entity\Provider;
use App\Repository\MerchandiseRepository;
use Doctrine\ORM\EntityManagerInterface;

class MerchandiseManager
{
    /** @var EntityManagerInterface  */
    private $em;

    /** @var MerchandiseRepository */
    private $repository;

    /** @var MerchandisePaymentManager */
    private $paymentManager;

    /** @var ProviderDebtManager */
    private $debtManager;

    public function __construct(
        EntityManagerInterface $em,
        MerchandiseRepository $repository,
        MerchandisePaymentManager $paymentManager,
        ProviderDebtManager $debtManager
    ) {
        $this->em = $em;
        $this->repository = $repository;
        $this->paymentManager = $paymentManager;
        $this->debtManager = $debtManager;
    }

    /**
     * Return merchandise for a specific day, grouped by provider.
     * @param $date
     * @return Provider[]
     */
    public function findForDay(\DateTimeInterface $date): array
    {
        return $this->em->getRepository(Provider::class)->findByDay($date);
    }

    public function createPaymentOrDebt(Merchandise $merchandise)
    {
        if($merchandise->getPaidWith() === Merchandise::PAID_WITH_DEBT) {
            $this->debtManager->update($merchandise);
        } else {
            $this->paymentManager->create($merchandise);
        }
    }
}
