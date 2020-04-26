<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\Merchandise;
use App\Entity\MerchandiseCategory;
use App\Entity\MerchandisePayment;
use App\Entity\Money;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MonthlyReportController extends AbstractController
{
    /**
     * @Route("/reports/monthly/{year}", name="monthly_report")
     */
    public function report($year)
    {
        // TODO refactor this to month name and change in repositories, than translate
        $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        $data = [];

        $em = $this->getDoctrine()->getManager();
        $money = $em->getRepository(Money::class)->getMonthlySum($year);
        $expense = $em->getRepository(Expense::class)->getMonthlySum($year);
        $payments = $em->getRepository(MerchandisePayment::class)->getMonthlySum($year);
        $merchandise = $em->getRepository(Merchandise::class)->getMonthlySum($year);

        // TODO move this to domain service?
        foreach ($months as $month) {
            $data[$month] = [
                'money' => $money[$month] ?? 0,
                'expenses' => $expense[$month] ?? 0,
                'payments_invoice' => $payments[$month][1] ?? 0,
                'payments_bill' => $payments[$month][2] ?? 0,
                'profit' => $merchandise[$month] ?? 0
            ];

            $data[$month]['sales'] = $data[$month]['money'] + $data[$month]['expenses'] + $data[$month]['payments_invoice']
                + $data[$month]['payments_bill'];
        }

        return $this->render('reports/monthly/general.html.twig', [
            'data' => $data
        ]);
    }

    /**
     * @Route("/reports/monthly/expenses/{year}", name="monthly_expenses_report")
     */
    public function expenses($year)
    {
        $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

        $em = $this->getDoctrine()->getManager();

        // TODO group inside category the merchandise indexed by month
        $merchandise = $em->getRepository(Merchandise::class)->getMonthlySum($year, true);
        $categories = $em->getRepository(MerchandiseCategory::class)->findAll();

        return $this->render('reports/monthly/expenses.html.twig', [
            'months' => $months,
            'merchandise' => $merchandise,
            'categories' => $categories
        ]);
    }
}
