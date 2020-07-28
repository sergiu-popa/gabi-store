<?php

namespace App\Controller;

use App\Entity\Balance;
use App\Manager\DayManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/balance")
 */

class BalanceController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var \App\Repository\BalanceRepository */
    private $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Balance::class);
    }

    /**
     * @Route("/", name="balance", methods={"GET"})
     */
    public function show(): Response
    {
        return $this->render('balance.html.twig', [
            'balances' => $this->repository->findAll(),
        ]);
    }

    /**
     * @Route("/recalculate/{date}", name="balance_recalculate", methods={"GET"})
     */
    public function update($date, DayManager $dayManager): Response
    {
        $balances = $this->repository->findFromDate($date);
        $count = 0;

        foreach($balances as $balance) {
            $transactions = $dayManager->getTransactions($balance->getDate());
            $recalculatedBalance = $transactions['totals']['balance'];

            if($recalculatedBalance !== $balance->getAmount()) {
                $balance->setAmount($recalculatedBalance);
                $count++;
            }

            $this->em->flush();
        }

        $this->addFlash('success', 'Toate soldurile au fost recalculate. Actualizări: '.$count);

        return $this->redirectToRoute('balance');
    }
}
