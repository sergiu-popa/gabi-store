<?php

namespace App\Controller;

use App\Manager\DayManager;
use App\Repository\BalanceRepository;
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
    /**
     * @Route("/", name="balance", methods={"GET"})
     */
    public function show(BalanceRepository $repository): Response
    {
        return $this->render('balance.html.twig', [
            'balances' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/recalculate", name="balance_recalculate", methods={"GET"})
     */
    public function update(BalanceRepository $repository, DayManager $dayManager, EntityManagerInterface $em): Response
    {
        $balances = $repository->findLastMonth();

        foreach($balances as $balance) {
            $recalculatedBalance = $dayManager->getTransactions($balance->getDate());
            $balance->setRecalculatedAmount($recalculatedBalance['totals']['balance']);
            $balance->setAmount($recalculatedBalance['totals']['balance']);

            $em->flush();
        }

        $this->addFlash('success', 'Soldurile au fost recalculate pentru ultima lună.');

        return $this->redirectToRoute('balance');
    }
}
