<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\Merchandise;
use App\Entity\MerchandisePayment;
use App\Entity\Money;
use App\Domain\Month;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ReportsController extends AbstractController
{
    /**
     * @Route("/reports/{year}/{month}", name="reports")
     */
    public function index($year = '2020', $month = '04')
    {
        //$month = '04'; // TODO aprilie -> 04
        $em = $this->getDoctrine()->getManager();

        $money = $em->getRepository(Money::class)->getForYearAndMonth($year, $month);
        $expenses = $em->getRepository(Expense::class)->getForYearAndMonth($year, $month);
        $merchandise = $em->getRepository(Merchandise::class)->getForYearAndMonth($year, $month);
        $merchandisePayments = $em->getRepository(MerchandisePayment::class)->getForYearAndMonth($year, $month);

        // Foreach day set expenses, set merchandise, set merchandise payments, set money
        $month = new Month($year, $month);
        $month->addItemsInEachDay('money', $money);
        $month->addItemsInEachDay('expense', $expenses);
        $month->addItemsInEachDay('merchandise', $merchandise);
        $month->addItemsInEachDay('merchandisePayments', $merchandisePayments);

        // TODO highchart line
        return $this->render('reports/index.html.twig', [
            'month' => $month
        ]);
    }
}
