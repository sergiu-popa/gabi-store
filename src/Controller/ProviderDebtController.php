<?php

namespace App\Controller;

use App\Entity\Provider;
use App\Entity\ProviderDebt;
use App\Manager\ProviderDebtManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/provider/debt")
 */
class ProviderDebtController extends AbstractController
{
    /** @var ProviderDebtManager */
    private $manager;

    public function __construct(ProviderDebtManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/", name="provider_debt_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('provider_debt.html.twig', [
            'providers' => $this->manager->findUnpaid(),
            'total' => $this->manager->findUnpaidTotalAmount()
        ]);
    }

    /**
     * @Route("/{id}/plati", name="provider_debt_payments_history", methods={"GET"})
     */
    public function paymentsHistory(Provider $provider): Response
    {
        return $this->render('provider_debt_payments.html.twig', [
            'provider' => $provider,
            'debts' => $this->manager->findDebtsWithPayments($provider)
        ]);
    }

    /**
     * @Route("/{id}/pay", name="provider_debt_pay", methods={"POST"})
     */
    public function edit(Request $request, ProviderDebt $debt): Response
    {
        if ($this->isCsrfTokenValid('pay'.$debt->getId(), $request->request->get('_token'))) {
            $type = $request->request->get('type');
            $amount = $request->request->get('amount');

            if($type === 'partially' && $amount > $debt->getAmount()) {
                $this->addFlash('error', 'Plata parțială nu poate fi mai mare decât datoria totală.');

                return $this->redirectToRoute('provider_debt_index');
            }

            $this->manager->pay($debt, $type, $amount);

            $this->addFlash('success', 'Datoria a fost plătită ' . ($type === 'fully' ? 'total' : 'parțial'));
        }

        return $this->redirectToRoute('provider_debt_index');
    }
}
