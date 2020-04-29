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

    public function __construct(EntityManagerInterface $em, MerchandiseRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
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
}
