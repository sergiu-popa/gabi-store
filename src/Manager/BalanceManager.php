<?php

namespace App\Manager;

use App\Entity\Balance;
use Doctrine\ORM\EntityManagerInterface;

class BalanceManager
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function addBalanceForToday(float $amount)
    {
        $balance = new Balance();
        $balance->setDate(new \DateTime());
        $balance->setAmount($amount);

        $this->em->persist($balance);
        $this->em->flush();
    }
}
