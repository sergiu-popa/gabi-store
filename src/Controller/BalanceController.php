<?php

namespace App\Controller;

use App\Entity\Balance;
use App\Form\BalanceType;
use App\Repository\BalanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/balance")
 */
class BalanceController extends AbstractController
{
    /**
     * @Route("/", name="balance_index", methods={"GET"})
     */
    public function index(BalanceRepository $balanceRepository): Response
    {
        // TODO pagination
        return $this->render('balance/index.html.twig', [
            'balances' => $balanceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="balance_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $balance = new Balance();
        $form = $this->createForm(BalanceType::class, $balance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($balance);
            $entityManager->flush();

            return $this->redirectToRoute('balance_index');
        }

        return $this->render('balance/new.html.twig', [
            'balance' => $balance,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="balance_show", methods={"GET"})
     */
    public function show(Balance $balance): Response
    {
        return $this->render('balance/show.html.twig', [
            'balance' => $balance,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="balance_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Balance $balance): Response
    {
        $form = $this->createForm(BalanceType::class, $balance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('balance_index');
        }

        return $this->render('balance/edit.html.twig', [
            'balance' => $balance,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="balance_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Balance $balance): Response
    {
        if ($this->isCsrfTokenValid('delete'.$balance->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($balance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('balance_index');
    }
}
