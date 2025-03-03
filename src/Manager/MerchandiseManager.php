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
        if($merchandise->isDebt()) {
            $this->debtManager->add($merchandise);
        } else {
            $this->paymentManager->add($merchandise);
        }
    }

    public function update(int $previousTotalEnterValue, Merchandise $merchandise)
    {
        if ($merchandise->isDebt()) {
            $this->debtManager->update($previousTotalEnterValue, $merchandise);
        } else {
            $this->paymentManager->update($previousTotalEnterValue, $merchandise);
        }

        $this->em->flush();
    }

    public function delete(Merchandise $merchandise)
    {
        $merchandise->delete();

        if ($merchandise->isDebt()) {
            $this->debtManager->delete($merchandise);
        } else {
            $this->paymentManager->delete($merchandise);
        }

        $this->em->flush();
    }
}
