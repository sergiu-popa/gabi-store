<?php

namespace App\Manager;

use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProviderManager
{
    /** @var EntityManagerInterface */
    private $repository;

    public function __construct(ProviderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getForDay(\DateTime $date)
    {
        return $this->repository->findForToday($date->format('l'));
    }
}
