<?php

namespace App\Controller;

use App\Manager\DayManager;
use App\Repository\BalanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/balance")
 */
class BalanceController extends AbstractController
{
    /**
     * @Route("/", name="balance", methods={"GET"})
     */
    public function show(BalanceRepository $repository): Response
    {
        return $this->render('balance/index.html.twig', [
            'balances' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/recalculate", name="balance_recalculate", methods={"GET"})
     */
    public function update(BalanceRepository $repository, DayManager $dayManager): Response
    {
        $balances = $repository->findLastMonth();

        foreach($balances as $balance) {
            $recalculatedBalance = $dayManager->getTransactions($balance->getDate());
            $balance->setRecalculatedAmount($recalculatedBalance['totals']['balance']);
        }

        // TODO From to confirm manually modified balances to update the database

        return $this->render('balance/update.html.twig', [
            'balances' => $balances,
        ]);
    }
}
