<?php

namespace App\Manager;

use App\Entity\Merchandise;
use App\Entity\ProviderDebt;
use Doctrine\ORM\EntityManagerInterface;

class ProviderDebtManager
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
}
