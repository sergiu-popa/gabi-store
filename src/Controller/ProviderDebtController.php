<?php

namespace App\Controller;

use App\Entity\ProviderDebt;
use App\Form\ProviderDebtType;
use App\Manager\ProviderDebtManager;
use App\Repository\ProviderDebtRepository;
use Doctrine\ORM\EntityManagerInterface;
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
            'providers' => $this->manager->findUnpaid()
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

            if($type === 'partially' && $amount > $debt->getRemainingAmount()) {
                $this->addFlash('error', 'Plata parțială nu poate fi mai mare decât datoria totală.');

                return $this->redirectToRoute('provider_debt_index');
            }

            $this->manager->pay($debt, $type, $amount);

            $this->addFlash('success', 'Datoria a fost plătită ' . ($type === 'fully' ? 'total' : 'parțial'));
        }

        return $this->redirectToRoute('provider_debt_index');
    }
}
