<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\Merchandise;
use App\Entity\MerchandisePayment;
use App\Entity\Money;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AnnualReportController extends AbstractController
{
    /**
     * @Route("/reports/annual", name="annual_report")
     */
    public function index()
    {
        $years = range(date('Y'), 2016);
        $data = [];

        $em = $this->getDoctrine()->getManager();
        $money = $em->getRepository(Money::class)->getYearlySum();
        $expense = $em->getRepository(Expense::class)->getYearlySum();
        $payments = $em->getRepository(MerchandisePayment::class)->getYearlySum();
        $merchandise = $em->getRepository(Merchandise::class)->getYearlySum();

        // TODO move this to domain service?
        foreach ($years as $year) {
            $data[$year] = [
                'money' => $money[$year] ?? 0,
                'expenses' => $expense[$year] ?? 0,
                'payments_invoice' => $payments[$year][1] ?? 0,
                'payments_bill' => $payments[$year][2] ?? 0,
                'profit' => $merchandise[$year] ?? 0
            ];

            $data[$year]['sales'] = $data[$year]['money'] + $data[$year]['expenses'] + $data[$year]['payments_invoice']
                + $data[$year]['payments_bill'];
        }

        return $this->render('reports/annual.html.twig', [
            'data' => $data
        ]);
    }
}
