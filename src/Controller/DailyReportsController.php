<?php

namespace App\Controller;

use App\Domain\Reports\MonthlyExpenses;
use App\Entity\ExpenseCategory;
use App\Entity\Expense;
use App\Entity\Merchandise;
use App\Entity\MerchandisePayment;
use App\Entity\Money;
use App\Domain\Month;
use App\Entity\Provider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DailyReportsController extends AbstractController
{
    /**
     * @Route("/reports/daily/{year}/{month}", name="report_sales", requirements={"year":"\d+"})
     */
    public function sales($year = null, $month = null)
    {
        $year = $year ?? date('Y');
        $month = $month ?? date('m');
        $yearMonth = $year . '-' . $month;

        //$month = '04'; // TODO aprilie -> 04
        $em = $this->getDoctrine()->getManager();

        // TODO move this to a service
        $money = $em->getRepository(Money::class)->getForYearAndMonth($year, $month);
        $expenses = $em->getRepository(Expense::class)->getForYearAndMonth($year, $month);
        $merchandise = $em->getRepository(Merchandise::class)->getForYearAndMonth($year, $month);
        $merchandisePayments = $em->getRepository(MerchandisePayment::class)
            ->getForYearAndMonth($year, $month);

        // Foreach day set expenses, set merchandise, set merchandise payments, set money
        $month = new Month($year, $month);
        $month->addItemsInEachDay('money', $money);
        $month->addItemsInEachDay('expense', $expenses);
        $month->addItemsInEachDay('merchandise', $merchandise);
        $month->addItemsInEachDay('merchandisePayments', $merchandisePayments);

        return $this->render('reports/daily/sales.html.twig', [
            'year_month' => $yearMonth,
            'month' => $month
        ]);
    }

    /**
     * @Route("/reports/daily/expenses/{year}/{month}", name="report_expenses")
     */
    public function expenses($year = null, $month = null)
    {
        $year = $year ?? date('Y');
        $month = $month ?? date('m');

        $em = $this->getDoctrine()->getManager();
        $expenses = $em->getRepository(Expense::class)->getForYearAndMonth($year, $month);
        $categories = $em->getRepository(ExpenseCategory::class)->findAll();

        $month = new MonthlyExpenses($year, $month, $categories, $expenses);

        return $this->render('reports/daily/expenses.html.twig', [
            'categories' => $categories,
            'month' => $month
        ]);
    }

    /**
     * @Route("/reports/daily/providers/{provider}/{year}/{month}", name="report_providers")
     */
    public function payments($provider = null, $year = null, $month = null)
    {
        $year = $year ?? date('Y');
        $month = $month ?? date('m');

        // TODO search box
        $em = $this->getDoctrine()->getManager();
        $provider = $em->getRepository(Provider::class)->find($provider);
        $merchandisePayments = $em->getRepository(MerchandisePayment::class)
            ->getForYearAndMonth($year, $month, $provider);

        $month = new Month($year, $month);
        $month->addItemsInEachDay('merchandisePayments', $merchandisePayments);

        // TODO chart
        return $this->render('reports/daily/providers.html.twig', [
            'month' => $month
        ]);
    }

    /**
     * @Route("/reports/daily/merchandise/{name}", name="report_merchandise")
     */
    public function merchandise($name = null)
    {
        $em = $this->getDoctrine()->getManager();
        $merchandise = $em->getRepository(Merchandise::class)->searchMerchandise($name);

        // TODO search and pagination
        return $this->render('reports/daily/merchandise.html.twig', [
            'merchandise' => $merchandise
        ]);
    }
}
