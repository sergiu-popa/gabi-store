<?php

namespace App\Controller;

use App\Entity\Balance;
use App\Entity\Expense;
use App\Entity\ExpenseCategory;
use App\Entity\Merchandise;
use App\Entity\MerchandisePayment;
use App\Entity\Money;
use App\Manager\ProviderDebtManager;
use App\Util\Months;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MonthlySituationController extends AbstractController
{
    /**
     * @Route("/monthly/situation", name="monthly_situation")
     */
    public function month(Request $request, ProviderDebtManager $providerDebtManager)
    {
        $year = $request->query->getInt('year', date('Y'));
        $month = $request->query->get('month', date('m'));

        $em = $this->getDoctrine()->getManager();
        $money = $em->getRepository(Money::class)->getMonthlySum($year);
        $expenses = $em->getRepository(Expense::class)->getMonthlySum($year);
        $payments = $em->getRepository(MerchandisePayment::class)->getMonthlySum($year);
        $merchandise = $em->getRepository(Merchandise::class)->getMonthlySum($year);

        $moneyTotal = $money[$month] ?? 0;
        $expensesTotal = $expenses[$month] ?? 0;
        $paymentsTotal = $payments[$month] ?? 0;
        $merchandiseTotal = $merchandise[$month] ?? 0;
        $debtTotal = $providerDebtManager->findUnpaidTotalAmount();

        $expensesCategories = $em->getRepository(ExpenseCategory::class)->findAll();
        $expensesByCategory = $em->getRepository(Expense::class)->getMonthlyCategories($year, $expensesCategories);

        $balanceRepository = $em->getRepository(Balance::class);
        $firstDayOfMonth = new \DateTime(sprintf('first day of %s %d', Months::getByNumber($month), $year));
        $initialBalance = $balanceRepository->findByDay($firstDayOfMonth) ?? $balanceRepository->findFirstAfterDate($firstDayOfMonth);
        $lastDayOfMonth = new \DateTime(sprintf('last day of %s %d', Months::getByNumber($month), $year));
        $lastBalance = $balanceRepository->findByDay($lastDayOfMonth) ?? $balanceRepository->findLastBeforeDate($lastDayOfMonth);

        return $this->render('monthly_situation.html.twig', [
            'years' => range(date('Y'), 2016),
            'year' => $year,
            'months' => Months::get(),
            'month' => $month,
            'sales' => $moneyTotal + $expensesTotal + $paymentsTotal[1] + $paymentsTotal[2],
            'moneyTotal' => $moneyTotal,
            'expenseTotal' => $expensesTotal,
            'paymentsTotal' => $paymentsTotal,
            'merchandiseTotal' => $merchandiseTotal,
            'debtTotal' => $debtTotal,
            'expensesCategories' => $expensesCategories,
            'expensesByCategory' => $expensesByCategory[$month] ?? 0,
            'initialBalance' => $initialBalance->getAmount(),
            'lastBalance' => $lastBalance->getAmount()
        ]);
    }
}
