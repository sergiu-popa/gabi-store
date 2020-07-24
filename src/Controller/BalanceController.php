<?php

namespace App\Controller;

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
        return $this->render('balance.html.twig', [
            'balances' => $repository->findAll(),
        ]);
    }

    // TODO trigger update sold for each day and create snapshots?
}
