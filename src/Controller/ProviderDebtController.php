<?php

namespace App\Controller;

use App\Entity\ProviderDebt;
use App\Form\DebtType;
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
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="debt_index", methods={"GET"})
     */
    public function index(ProviderDebtRepository $debtRepository): Response
    {
        return $this->render('provider_debt/index.html.twig', [
            'debts' => $debtRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="debt_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $debt = new ProviderDebt();
        $form = $this->createForm(DebtType::class, $debt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($debt);
            $this->em->flush();

            return $this->redirectToRoute('debt_index');
        }

        return $this->render('provider_debt/new.html.twig', [
            'debt' => $debt,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="debt_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProviderDebt $debt): Response
    {
        $form = $this->createForm(DebtType::class, $debt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('debt_index');
        }

        return $this->render('provider_debt/edit.html.twig', [
            'debt' => $debt,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="debt_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProviderDebt $debt): Response
    {
        if ($this->isCsrfTokenValid('delete'.$debt->getId(), $request->request->get('_token'))) {
            $debt->delete();
            $this->em->flush();

            return $this->json(['success' => true, 'message' => 'Datoria a fost ștearsă cu success.']);
        }

        return $this->json(['success' => false], Response::HTTP_BAD_REQUEST);
    }
}
