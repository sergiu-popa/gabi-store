<?php

namespace App\Controller;

use App\Domain\Reports\MonthlyExpenses;
use App\Entity\Category;
use App\Entity\Expense;
use App\Entity\Merchandise;
use App\Entity\MerchandisePayment;
use App\Entity\Money;
use App\Domain\Month;
use App\Entity\Provider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ReportsController extends AbstractController
{
    /**
     * @Route("/reports/{year}/{month}", name="reports_sales")
     */
    public function sales($year = '2020', $month = '04')
    {
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

        // TODO chart
        return $this->render('reports/sales.html.twig', [
            'month' => $month
        ]);
    }

    /**
     * @Route("/reports/expenses/{year}/{month}", name="reports_expenses")
     */
    public function expenses($year = '2020', $month = '04')
    {
        $em = $this->getDoctrine()->getManager();
        $expenses = $em->getRepository(Expense::class)->getForYearAndMonth($year, $month);
        $categories = $em->getRepository(Category::class)->findAll();

        $month = new MonthlyExpenses($year, $month);
        $month->addExpensesInEachDay($expenses);

        // TODO chart
        return $this->render('reports/expenses.html.twig', [
            'categories' => $categories,
            'month' => $month
        ]);
    }

    /**
     * @Route("/reports/providers/{provider}/{year}/{month}", name="reports_providers")
     */
    public function payments($provider, $year = '2020', $month = '04')
    {
        $em = $this->getDoctrine()->getManager();
        $provider = $em->getRepository(Provider::class)->find($provider);
        $merchandisePayments = $em->getRepository(MerchandisePayment::class)
            ->getForYearAndMonth($year, $month, $provider);

        $month = new Month($year, $month);
        $month->addItemsInEachDay('merchandisePayments', $merchandisePayments);

        // TODO chart
        return $this->render('reports/providers.html.twig', [
            'month' => $month
        ]);
    }


}
