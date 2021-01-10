<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\ExpenseCategory;
use App\Entity\Merchandise;
use App\Entity\MerchandiseCategory;
use App\Entity\MerchandisePayment;
use App\Entity\Money;
use App\Util\Months;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MonthlyReportController extends AbstractController
{
    /**
     * @Route("/reports/monthly/general", name="monthly_report", requirements={"year":"\d+"})
     */
    public function report(Request $request)
    {
        $year = $request->query->getInt('year', date('Y'));

        $months = Months::get();
        $data = [];

        $em = $this->getDoctrine()->getManager();
        $money = $em->getRepository(Money::class)->getMonthlySum($year);
        $expense = $em->getRepository(Expense::class)->getMonthlySum($year);
        $payments = $em->getRepository(MerchandisePayment::class)->getMonthlySum($year);
        $merchandise = $em->getRepository(Merchandise::class)->getMonthlySum($year);

        // TODO move this to domain service?
        foreach ($months as $month => $name) {
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
            'years' => range(date('Y'), 2016),
            'year' => $year,
            'months' => $months,
            'data' => $data
        ]);
    }

    /**
     * @Route("/reports/monthly/merchandise-categories", name="monthly_merchandise_categories_report")
     */
    public function merchandise(Request $request)
    {
        $year = $request->query->getInt('year', date('Y'));

        $em = $this->getDoctrine()->getManager();

        // TODO group inside category the merchandise indexed by month
        $merchandise = $em->getRepository(Merchandise::class)->getMonthlySum($year, true);
        $categories = $em->getRepository(MerchandiseCategory::class)->findAll();

        return $this->render('reports/monthly/merchandise-categories.html.twig', [
            'years' => range(date('Y'), 2016),
            'year' => $year,
            'months' => Months::get(),
            'merchandise' => $merchandise,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/reports/monthly/expenses", name="monthly_expenses_report")
     */
    public function expenses(Request $request)
    {
        $year = $request->query->getInt('year', date('Y'));

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(ExpenseCategory::class)->findAll();
        $expenses = $em->getRepository(Expense::class)->getMonthlyCategories($year, $categories);

        return $this->render('reports/monthly/expenses.html.twig', [
            'years' => range(date('Y'), 2016),
            'year' => $year,
            'months' => Months::get(),
            'expenses' => $expenses,
            'categories' => $categories,
        ]);
    }
}
